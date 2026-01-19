<?php

namespace App\Services\Statistic;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * PerformanceStatisticService
 *
 * Service untuk mengelola statistik kinerja mentoring (MODULE D: Admin Dashboard)
 * Menyediakan data analisis aktivitas mentor dan kinerja kelompok mentoring
 *
 * Fungsi utama:
 * - getAnalysis(): Menggabungkan analisis aktivitas mentor dan kinerja kelompok
 * - getMentorActivity(): Menganalisis aktivitas mentor
 * - getGroupPerformance(): Menganalisis kinerja kelompok berdasarkan progres skor
 *
 * Data yang dihasilkan:
 * - Statistik aktivitas mentor
 * - Daftar mentor paling aktif
 * - Daftar mentor yang perlu perhatian
 * - Data kinerja kelompok
 * - Daftar kelompok dengan progres baik
 * - Daftar kelompok stagnan
 *
 * @package App\Services\Statistic
 */
class PerformanceStatisticService
{
    /**
     * Mengembalikan analisis aktivitas mentor dan kinerja kelompok
     *
     * Proses:
     * 1. Ambil data aktivitas mentor
     * 2. Ambil data kinerja kelompok
     * 3. Gabungkan kedua data dan kembalikan
     *
     * @return array Array berisi data aktivitas mentor dan kinerja kelompok
     */
    public function getAnalysis(): array
    {
        $mentorActivity = $this->getMentorActivity();
        $groupPerformance = $this->getGroupPerformance();

        return array_merge($mentorActivity, $groupPerformance);
    }

    /**
     * Menganalisis aktivitas mentor
     *
     * Proses:
     * 1. Ambil semua mentor dari database
     * 2. Hitung jumlah laporan yang diisi dan tingkat kehadiran untuk setiap mentor
     * 3. Buat statistik untuk setiap mentor
     * 4. Identifikasi mentor paling aktif dan mentor yang perlu perhatian
     * 5. Kembalikan data mentor paling aktif dan mentor yang perlu perhatian
     *
     * @return array Array berisi data mentor paling aktif dan mentor yang perlu perhatian
     */
    private function getMentorActivity(): array
    {
        // Ambil semua mentor (role_id = 2) beserta relasi yang diperlukan
        $mentors = User::where('role_id', 2) // Role 2 untuk Mentor
            ->with([
                'mentoringGroupsAsMentor.sessions.attendances', // Kehadiran di sesi
                'mentoringGroupsAsMentor.sessions.progressReports', // Laporan progres
                'faculty', // Fakultas mentor
                'mentoringGroupsAsMentor.members' // Anggota kelompok
            ])
            ->get();

        // Hitung statistik aktivitas untuk setiap mentor
        $mentorStats = $mentors->map(function ($mentor) {
            $totalReportsFilled = 0; // Jumlah laporan yang diisi
            $totalPossibleAttendances = 0; // Jumlah kemungkinan kehadiran
            $totalPresentAttendances = 0; // Jumlah kehadiran aktual

            // Iterasi semua kelompok mentoring yang dipimpin mentor
            foreach ($mentor->mentoringGroupsAsMentor as $group) {
                // Iterasi semua sesi dalam setiap kelompok
                foreach ($group->sessions as $session) {
                    $totalReportsFilled += $session->progressReports->count(); // Tambahkan jumlah laporan
                    $totalPossibleAttendances += $group->members->count(); // Tambahkan jumlah kemungkinan kehadiran
                    $totalPresentAttendances += $session->attendances->where('status', 'hadir')->count(); // Tambahkan kehadiran aktual
                }
            }

            // Hitung rata-rata tingkat kehadiran
            $avgAttendanceRate = $totalPossibleAttendances > 0 ? round(($totalPresentAttendances / $totalPossibleAttendances) * 100) : 0;

            // Kembalikan data statistik untuk mentor ini
            return [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'faculty' => $mentor->faculty->name ?? 'N/A', // Nama fakultas atau 'N/A' jika tidak ada
                'groups_count' => $mentor->mentoringGroupsAsMentor->count(), // Jumlah kelompok yang dipimpin
                'reports_filled' => $totalReportsFilled, // Jumlah laporan yang diisi
                'avg_attendance_rate' => $avgAttendanceRate, // Rata-rata tingkat kehadiran
            ];
        });

        // Ambil 10 mentor paling aktif berdasarkan jumlah laporan yang diisi
        $mostActiveMentors = $mentorStats->sortByDesc('reports_filled')->take(10);

        // Filter mentor yang perlu perhatian (kurang dari 2 laporan atau tingkat kehadiran kurang dari 50%)
        $mentorsNeedingAttention = $mentorStats->filter(function($stat) {
            return $stat['reports_filled'] < 2 || $stat['avg_attendance_rate'] < 50;
        })->sortBy('avg_attendance_rate');

        // Kembalikan data mentor paling aktif dan mentor yang perlu perhatian
        return compact('mostActiveMentors', 'mentorsNeedingAttention');
    }

    /**
     * Menganalisis kinerja kelompok berdasarkan progres skor
     *
     * Proses:
     * 1. Ambil data semua kelompok beserta skor placement test dan ujian akhir
     * 2. Hitung peningkatan skor untuk setiap kelompok
     * 3. Kategorikan kelompok berdasarkan peningkatan skor
     * 4. Identifikasi kelompok dengan progres baik dan kelompok stagnan
     * 5. Kembalikan data kelompok dengan progres baik dan kelompok stagnan
     *
     * @return array Array berisi data kelompok dengan progres baik dan kelompok stagnan
     */
    private function getGroupPerformance(): array
    {
        // Ambil data semua kelompok beserta skor placement test dan ujian akhir
        $allGroupsData = DB::table('mentoring_groups')
            ->join('users as mentors', 'mentoring_groups.mentor_id', '=', 'mentors.id')
            ->join('group_members', 'mentoring_groups.id', '=', 'group_members.mentoring_group_id')
            ->join('users as mentees', 'group_members.mentee_id', '=', 'mentees.id')
            ->leftJoin('placement_tests', 'mentees.id', '=', 'placement_tests.mentee_id')
            ->leftJoin('exam_submissions', 'mentees.id', '=', 'exam_submissions.mentee_id')
            ->where('exam_submissions.status', 'graded') // Hanya ambil ujian akhir yang sudah dinilai
            ->whereNotNull('placement_tests.audio_reading_score') // Pastikan skor placement test tersedia
            ->whereNotNull('placement_tests.theory_score') // Pastikan skor theory test tersedia
            ->select(
                'mentoring_groups.id as group_id',
                'mentoring_groups.name as group_name',
                'mentors.name as mentor_name',
                DB::raw('AVG((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as avg_placement_score'), // Skor rata-rata placement test
                DB::raw('AVG(exam_submissions.total_score) as avg_final_exam_score') // Skor rata-rata ujian akhir
            )
            ->groupBy('mentoring_groups.id', 'mentoring_groups.name', 'mentors.name')
            ->get();

        // Proses data untuk menghitung peningkatan skor dan kategorisasi
        $groupPerformance = $allGroupsData->map(function ($group) {
            $placementScore = $group->avg_placement_score;
            $finalExamScore = $group->avg_final_exam_score;
            $pointIncrease = $finalExamScore - $placementScore; // Hitung peningkatan skor

            // Tambahkan properti peningkatan skor ke objek grup
            $group->avg_score_increase_points = $pointIncrease;
            $group->avg_score_increase_percentage = $placementScore > 0 ? ($pointIncrease / $placementScore) * 100 : 0;

            // Kategorikan kelompok berdasarkan peningkatan skor
            if ($pointIncrease > 15) {
                $group->category = 'Progres Sangat Baik';
            } elseif ($pointIncrease > 5) {
                $group->category = 'Progres Baik';
            } elseif ($pointIncrease >= -5) {
                $group->category = 'Stagnan';
            } else {
                $group->category = 'Perlu Perhatian';
            }

            return $group;
        });

        // Ambil 10 kelompok dengan peningkatan skor tertinggi
        $progressiveGroups = $groupPerformance->sortByDesc('avg_score_increase_points')->take(10);

        // Ambil 10 kelompok dengan peningkatan skor terendah (termasuk negatif)
        $stagnantGroups = $groupPerformance->sortBy('avg_score_increase_points')->take(10);

        // Kembalikan data kelompok dengan progres baik dan kelompok stagnan
        return compact('progressiveGroups', 'stagnantGroups');
    }
}
