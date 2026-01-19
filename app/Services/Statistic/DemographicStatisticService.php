<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;

/**
 * DemographicStatisticService
 *
 * Service untuk mengelola statistik demografi mentee (MODULE D: Admin Dashboard)
 * Menyediakan data statistik berdasarkan fakultas dan program studi
 *
 * Fungsi utama:
 * - getFacultyStats(): Menghitung statistik berdasarkan fakultas
 * - getProgramStats(): Menghitung statistik berdasarkan program studi
 *
 * Data yang dihasilkan:
 * - Jumlah mentee per fakultas
 * - Skor rata-rata placement test per fakultas
 * - Jumlah mentee per program studi
 * - Skor rata-rata placement test per program studi
 * - Interpretasi statistik
 *
 * @package App\Services\Statistic
 */
class DemographicStatisticService
{
    /**
     * Menghitung dan mengembalikan statistik berdasarkan fakultas termasuk jumlah mentee dan skor rata-rata
     *
     * Proses:
     * 1. Ambil data dari tabel users, faculties, dan placement_tests
     * 2. Gabungkan data berdasarkan relasi antar tabel
     * 3. Hitung jumlah mentee dan skor rata-rata per fakultas
     * 4. Buat interpretasi berdasarkan data statistik
     * 5. Kembalikan array berisi statistik dan interpretasi
     *
     * @return array Array berisi statistik fakultas dan interpretasi
     */
    public function getFacultyStats(): array
    {
        // Ambil data statistik fakultas dari tabel users, faculties, dan placement_tests
        $facultyStats = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->select(
                'faculties.name as faculty_name',
                DB::raw('COUNT(DISTINCT users.id) as mentee_count'),
                DB::raw('AVG(placement_tests.audio_reading_score) as avg_audio_score'),
                DB::raw('AVG(placement_tests.theory_score) as avg_theory_score')
            )
            ->whereNotNull('placement_tests.audio_reading_score')
            ->whereNotNull('placement_tests.theory_score')
            ->groupBy('faculties.name')
            ->orderBy('faculties.name')
            ->get();

        // Buat interpretasi berdasarkan data statistik fakultas
        $interpretation = [];
        if ($facultyStats->isNotEmpty()) {
            // Temukan fakultas dengan jumlah mentee terbanyak
            $topFacultyMentee = $facultyStats->sortByDesc('mentee_count')->first();
            $interpretation[] = "Fakultas dengan partisipasi mentee terbanyak adalah **{$topFacultyMentee->faculty_name}** (" . number_format($topFacultyMentee->mentee_count) . " orang).";

            // Temukan fakultas dengan skor rata-rata tertinggi
            $facultyWithHighestScore = $facultyStats->map(function ($f) {
                $f->combined_score = ($f->avg_audio_score + $f->avg_theory_score) / 2;
                return $f;
            })->sortByDesc('combined_score')->first();
            $interpretation[] = "Dari segi performa, **{$facultyWithHighestScore->faculty_name}** menunjukkan skor rata-rata Placement Test tertinggi (**" . number_format($facultyWithHighestScore->combined_score, 1) . "**).";
        }

        // Kembalikan data statistik dan interpretasi
        return [
            'stats' => $facultyStats,
            'interpretation' => $interpretation,
        ];
    }

    /**
     * Menghitung dan mengembalikan statistik berdasarkan program studi termasuk jumlah mentee dan skor rata-rata
     *
     * Proses:
     * 1. Ambil data dari tabel users dan placement_tests
     * 2. Gabungkan data berdasarkan relasi antar tabel
     * 3. Hitung jumlah mentee dan skor rata-rata per program studi
     * 4. Urutkan hasil berdasarkan nama program studi
     * 5. Kembalikan koleksi data statistik
     *
     * @return \Illuminate\Support\Collection Koleksi data statistik program studi
     */
    public function getProgramStats(): \Illuminate\Support\Collection
    {
        // Ambil data statistik program studi dari tabel users dan placement_tests
        return DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->select(
                'users.program_study',
                DB::raw('COUNT(DISTINCT users.id) as mentee_count'),
                DB::raw('AVG(placement_tests.audio_reading_score) as avg_audio_score'),
                DB::raw('AVG(placement_tests.theory_score) as avg_theory_score')
            )
            ->whereNotNull('users.program_study')
            ->whereNotNull('placement_tests.audio_reading_score')
            ->whereNotNull('placement_tests.theory_score')
            ->groupBy('users.program_study')
            ->orderBy('users.program_study')
            ->get();
    }
}
