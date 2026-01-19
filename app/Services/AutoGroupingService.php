<?php

namespace App\Services;

use App\Models\User;
use App\Models\MentoringGroup;
use App\Models\GroupMember;
use App\Models\PlacementTest; // Model untuk mendapatkan level akhir dari placement test
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection; // Import Collection untuk pengelompokan data

/**
 * AutoGroupingService
 *
 * Service untuk mengelola otomatisasi pengelompokan mentee dan mentor (MODULE B: Mentee Management)
 * Menyediakan fitur untuk mengelompokan mentee berdasarkan fakultas, level, dan jenis kelamin
 * serta menetapkan mentor yang tersedia ke kelompok-kelompok tersebut
 *
 * Fungsi utama:
 * - handle(): Menjalankan proses pengelompokan otomatis
 * - clearExistingGroups(): Menghapus semua kelompok yang sudah ada
 * - getAvailableMentors(): Mendapatkan mentor yang tersedia
 * - getUnassignedMentees(): Mendapatkan mentee yang belum dikelompokkan
 * - groupMenteesByCriteria(): Mengelompokkan mentee berdasarkan kriteria
 * - assignMentor(): Menetapkan mentor ke kelompok
 * - createMentoringGroup(): Membuat kelompok mentoring baru
 *
 * @package App\Services
 */
class AutoGroupingService
{
    /**
     * Menangani proses pengelompokan otomatis mentee dan mentor
     *
     * Proses:
     * 1. Mulai transaksi database
     * 2. Hapus kelompok yang sudah ada jika diperlukan
     * 3. Ambil mentor dan mentee yang tersedia
     * 4. Validasi ketersediaan mentor dan mentee
     * 5. Kelompokkan mentor berdasarkan fakultas dan buat pool umum
     * 6. Kelompokkan mentee berdasarkan kriteria (fakultas, level, jenis kelamin)
     * 7. Untuk setiap kelompok mentee, buat kelompok mentoring dengan mentor yang sesuai
     * 8. Commit transaksi jika berhasil, rollback jika gagal
     *
     * @param int $menteesPerGroup Jumlah mentee yang diinginkan per kelompok
     * @param bool $deleteAllExisting Apakah akan menghapus semua kelompok yang sudah ada sebelum membuat yang baru
     * @return array Array berisi jumlah kelompok yang dibuat dan jumlah mentee yang ditugaskan
     * @throws \Exception Jika tidak ada mentor atau mentee yang tersedia
     */
    public function handle(int $menteesPerGroup, bool $deleteAllExisting): array
    {
        // Mulai transaksi database untuk memastikan konsistensi data
        DB::beginTransaction();
        try {
            if ($deleteAllExisting) {
                $this->clearExistingGroups();
            }

            // Ambil mentor dan mentee yang tersedia
            $availableMentors = $this->getAvailableMentors();
            $unassignedMentees = $this->getUnassignedMentees();

            // Validasi ketersediaan mentor dan mentee
            if ($availableMentors->isEmpty()) {
                throw new \Exception('Tidak ada mentor yang tersedia untuk ditugaskan.');
            }
            if ($unassignedMentees->isEmpty()) {
                throw new \Exception('Tidak ada mentee yang perlu dikelompokkan.');
            }

            // Kelompokkan mentor berdasarkan fakultas dan buat pool umum
            $mentorsByFaculty = $availableMentors->groupBy('faculty_id');
            $generalMentorPool = $availableMentors->shuffle();

            // Kelompokkan mentee berdasarkan kriteria (fakultas, level, jenis kelamin)
            $groupedMentees = $this->groupMenteesByCriteria($unassignedMentees);

            // Inisialisasi counter
            $groupsCreatedCount = 0;
            $menteesAssignedCount = 0;
            $totalUnassignedMenteesInitial = $unassignedMentees->count();

            // Proses setiap kelompok mentee
            foreach ($groupedMentees as $key => $menteesInGroup) {
                // Ekstrak kriteria dari key
                list($facultyId, $levelId, $gender) = explode('_', $key);

                // Bagi mentee dalam kelompok menjadi potongan-potongan sesuai jumlah per kelompok
                $chunks = $menteesInGroup->chunk($menteesPerGroup);

                foreach ($chunks as $chunk) {
                    // Tetapkan mentor untuk kelompok ini
                    $mentor = $this->assignMentor($facultyId, $mentorsByFaculty, $generalMentorPool);

                    if (!$mentor) {
                        // Jika tidak ada mentor tersedia, hentikan proses
                        Log::warning('AutoGroupingService: No mentor found for a mentee chunk. Stopping process.');
                        break 2;
                    }

                    // Buat kelompok mentoring baru
                    $this->createMentoringGroup($mentor, $chunk, $groupsCreatedCount, $levelId, $facultyId, $gender);
                    $groupsCreatedCount++;
                    $menteesAssignedCount += $chunk->count();
                }
            }

            // Commit transaksi jika semua proses berhasil
            DB::commit();

            // Kembalikan hasil proses
            return [
                'groups_created' => $groupsCreatedCount,
                'mentees_assigned' => $menteesAssignedCount,
                'mentors_assigned' => $groupsCreatedCount,
                'mentors_exhausted' => $generalMentorPool->isEmpty() && $menteesAssignedCount < $totalUnassignedMenteesInitial,
                'total_unassigned_mentees_initial' => $totalUnassignedMenteesInitial,
            ];

        } catch (\Throwable $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Auto grouping failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \Exception('Terjadi kesalahan saat membuat kelompok: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus semua kelompok dan anggota kelompok yang sudah ada
     *
     * Proses:
     * 1. Hapus semua data anggota kelompok
     * 2. Hapus semua data kelompok mentoring
     */
    private function clearExistingGroups(): void
    {
        // Hapus semua data anggota kelompok
        GroupMember::query()->delete();
        // Hapus semua data kelompok mentoring
        MentoringGroup::query()->delete();
    }

    /**
     * Mendapatkan mentor yang tersedia (role_id 2) yang belum ditugaskan ke kelompok manapun
     *
     * Proses:
     * 1. Ambil ID mentor yang sudah ditugaskan ke kelompok
     * 2. Ambil semua mentor (role_id = 2) yang tidak termasuk dalam daftar tersebut
     *
     * @return Collection Koleksi mentor yang tersedia
     */
    private function getAvailableMentors(): Collection
    {
        // Ambil ID mentor yang sudah ditugaskan ke kelompok
        $assignedMentorIds = MentoringGroup::pluck('mentor_id')->unique();

        // Ambil semua mentor (role_id = 2) yang tidak ditugaskan ke kelompok manapun
        return User::where('role_id', 2) // Mentor
            ->whereNotIn('id', $assignedMentorIds)
            ->get();
    }

    /**
     * Mendapatkan mentee yang belum ditugaskan (role_id 3) beserta fakultas dan level akhir dari placement test
     *
     * Proses:
     * 1. Ambil ID mentee yang sudah ditugaskan ke kelompok
     * 2. Ambil semua mentee (role_id = 3) yang tidak termasuk dalam daftar tersebut
     * 3. Muat relasi fakultas dan level akhir placement test
     *
     * @return Collection Koleksi mentee yang belum ditugaskan
     */
    private function getUnassignedMentees(): Collection
    {
        // Ambil ID mentee yang sudah ditugaskan ke kelompok
        $assignedMenteeIds = GroupMember::pluck('mentee_id')->unique();

        // Ambil semua mentee (role_id = 3) yang belum ditugaskan ke kelompok manapun
        return User::where('role_id', 3) // Mentee
            ->whereNotIn('id', $assignedMenteeIds)
            ->with(['faculty', 'placementTest.finalLevel']) // Muat relasi fakultas dan level akhir placement test
            ->get();
    }

    /**
     * Mengelompokkan mentee berdasarkan kunci komposit: fakultas, level, dan jenis kelamin
     *
     * Proses:
     * 1. Untuk setiap mentee, buat kunci berdasarkan fakultas, level akhir, dan jenis kelamin
     * 2. Kelompokkan mentee berdasarkan kunci tersebut
     *
     * @param Collection $mentees Koleksi mentee yang akan dikelompokkan
     * @return Collection Koleksi mentee yang telah dikelompokkan berdasarkan kriteria
     */
    private function groupMenteesByCriteria(Collection $mentees): Collection
    {
        // Kelompokkan mentee berdasarkan kunci komposit: fakultas, level, dan jenis kelamin
        return $mentees->groupBy(function ($mentee) {
            // Ambil ID fakultas mentee (gunakan 'unknown' jika tidak tersedia)
            $facultyId = $mentee->faculty_id ?? 'unknown';

            // Ambil ID level akhir dari placement test (gunakan 'unknown' jika tidak tersedia)
            // Pastikan placementTest dan finalLevel tersedia sebelum mengakses propertinya
            $levelId = $mentee->placementTest->finalLevel->id ?? 'unknown';

            // Ambil jenis kelamin mentee (gunakan 'unknown' jika tidak tersedia)
            $gender = $mentee->gender ?? 'unknown';

            // Kembalikan kunci komposit untuk pengelompokan
            return "{$facultyId}_{$levelId}_{$gender}";
        });
    }

    /**
     * Menetapkan mentor dari pool fakultas spesifik atau pool umum
     *
     * Proses:
     * 1. Coba ambil mentor dari fakultas yang sama dengan mentee
     * 2. Jika tidak ada mentor dari fakultas yang sama, ambil dari pool umum
     * 3. Pastikan mentor tidak ditetapkan ganda
     *
     * @param string $facultyId ID fakultas mentee
     * @param Collection $mentorsByFaculty Koleksi mentor yang dikelompokkan berdasarkan fakultas
     * @param Collection $generalMentorPool Pool mentor umum
     * @return User|null Mentor yang ditetapkan atau null jika tidak ada mentor tersedia
     */
    private function assignMentor(string $facultyId, Collection &$mentorsByFaculty, Collection &$generalMentorPool): ?User
    {
        $mentor = null;

        // Coba ambil mentor dari fakultas yang sama
        if (isset($mentorsByFaculty[$facultyId]) && $mentorsByFaculty[$facultyId]->isNotEmpty()) {
            $mentor = $mentorsByFaculty[$facultyId]->shift(); // Ambil dan hapus mentor dari pool

            // Juga hapus mentor ini dari pool umum untuk menghindari penugasan ganda
            $generalMentorPool = $generalMentorPool->reject(fn($m) => $m->id === $mentor->id);
        }
        // Jika tidak ada mentor di fakultas yang sama, ambil dari pool umum
        elseif ($generalMentorPool->isNotEmpty()) {
            $mentor = $generalMentorPool->shift(); // Ambil dan hapus mentor dari pool

            // Juga hapus mentor ini dari grup fakultas aslinya jika dia ada di sana
            foreach ($mentorsByFaculty as $fId => $mentors) {
                $mentorsByFaculty[$fId] = $mentors->reject(fn($m) => $m->id === $mentor->id);
            }
        }

        return $mentor;
    }

    /**
     * Membuat kelompok mentoring baru dan menetapkan mentee ke dalamnya
     *
     * Proses:
     * 1. Buat nama kelompok berdasarkan level, fakultas, jenis kelamin, dan nomor urut
     * 2. Simpan data kelompok mentoring ke database
     * 3. Buat data anggota kelompok untuk setiap mentee dalam chunk
     *
     * @param User $mentor Mentor yang akan menangani kelompok ini
     * @param Collection $menteesChunk Potongan mentee yang akan dimasukkan ke dalam kelompok
     * @param int $groupsCreatedCount Jumlah kelompok yang sudah dibuat sebelumnya
     * @param string $levelId ID level dari mentee dalam kelompok ini
     * @param string $facultyId ID fakultas dari mentee dalam kelompok ini
     * @param string $gender Jenis kelamin dari mentee dalam kelompok ini
     */
    private function createMentoringGroup(User $mentor, Collection $menteesChunk, int $groupsCreatedCount, string $levelId, string $facultyId, string $gender): void
    {
        // Ambil mentee pertama untuk mendapatkan informasi nama fakultas dan level
        $firstMentee = $menteesChunk->first();
        $facultyName = $firstMentee->faculty->name ?? 'Unknown Faculty';
        $levelName = $firstMentee->placementTest->finalLevel->name ?? 'Unknown Level';

        // Tentukan nama jenis kelamin berdasarkan nilai gender
        $genderName = ($gender === 'male' || $gender === '1') ? 'Ikhwan' : 'Akhwat';

        // Buat data kelompok mentoring
        $group = MentoringGroup::create([
            'mentor_id' => $mentor->id,
            'name' => "{$levelName} - {$facultyName} - {$genderName} - " . ($groupsCreatedCount + 1), // Nama kelompok
            'level_id' => $levelId, // ID level dari mentee dalam kelompok ini
        ]);

        // Siapkan data anggota kelompok untuk disisipkan
        $groupMembersData = [];
        foreach ($menteesChunk as $mentee) {
            $groupMembersData[] = [
                'mentee_id' => $mentee->id, // ID mentee
                'mentoring_group_id' => $group->id, // ID kelompok mentoring
                'created_at' => now(), // Timestamp pembuatan
                'updated_at' => now(), // Timestamp pembaruan
            ];
        }

        // Masukkan semua data anggota kelompok sekaligus
        GroupMember::insert($groupMembersData);
    }
}