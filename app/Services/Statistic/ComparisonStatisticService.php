<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;

/**
 * ComparisonStatisticService
 *
 * Service untuk mengelola perbandingan skor antara placement test dan ujian akhir (MODULE D: Admin Dashboard)
 * Menyediakan data perbandingan skor untuk analisis perkembangan mentee
 *
 * Fungsi utama:
 * - getScoreComparison(): Menghitung dan mengembalikan data perbandingan skor serta interpretasi
 *
 * Data yang dihasilkan:
 * - Skor rata-rata placement test per program studi
 * - Skor rata-rata ujian akhir per program studi
 * - Interpretasi perbandingan skor
 *
 * @package App\Services\Statistic
 */
class ComparisonStatisticService
{
    /**
     * Menghitung dan mengembalikan data perbandingan skor serta interpretasi antara placement test dan ujian akhir
     *
     * Proses:
     * 1. Ambil skor rata-rata placement test per program studi
     * 2. Ambil skor rata-rata ujian akhir per program studi
     * 3. Gabungkan data dari kedua jenis ujian menjadi satu array
     * 4. Buat interpretasi berdasarkan perbandingan skor
     * 5. Kembalikan array berisi data perbandingan dan interpretasi
     *
     * @return array Array berisi data perbandingan skor dan interpretasi
     */
    public function getScoreComparison(): array
    {
        // Ambil skor rata-rata placement test per program studi
        $placementScores = DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->whereNotNull('users.program_study')
            ->select(
                'users.program_study',
                DB::raw('AVG((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as avg_score')
            )
            ->groupBy('users.program_study')
            ->pluck('avg_score', 'program_study');

        // Ambil skor rata-rata ujian akhir per program studi
        $finalExamScores = DB::table('users')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->whereNotNull('users.program_study')
            ->where('exam_submissions.status', 'graded')
            ->select(
                'users.program_study',
                DB::raw('AVG(exam_submissions.total_score) as avg_score')
            )
            ->groupBy('users.program_study')
            ->pluck('avg_score', 'program_study');

        // Gabungkan semua program studi dari kedua jenis ujian
        $allPrograms = $placementScores->keys()->merge($finalExamScores->keys())->unique()->sort();

        // Gabungkan data skor untuk setiap program studi
        $scoreComparisonData = [];
        foreach($allPrograms as $program) {
            $scoreComparisonData[$program] = [
                'placement' => $placementScores[$program] ?? 0,  // Skor placement test, default 0 jika tidak ada
                'final_exam' => $finalExamScores[$program] ?? 0, // Skor ujian akhir, default 0 jika tidak ada
            ];
        }

        // Buat interpretasi berdasarkan data perbandingan skor
        $interpretation = [];
        if (!empty($scoreComparisonData)) {
            // Siapkan data untuk analisis interpretasi
            $allProgramsData = collect($scoreComparisonData);

            // Hitung rata-rata skor untuk masing-masing jenis ujian
            $avgPlacement = $allProgramsData->avg('placement');
            $avgFinal = $allProgramsData->avg('final_exam');

            // Tambahkan interpretasi berdasarkan perbandingan rata-rata skor
            if ($avgFinal > $avgPlacement) {
                $interpretation[] = "Secara umum, terdapat peningkatan performa mentee dengan rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih tinggi dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**).";
            } else {
                $interpretation[] = "Perlu diperhatikan, rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih rendah dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**) secara keseluruhan.";
            }

            // Temukan program studi dengan peningkatan skor terbesar
            $bestImprovement = $allProgramsData->map(function ($scores, $program) {
                return ['program' => $program, 'improvement' => $scores['final_exam'] - $scores['placement']];
            })->sortByDesc('improvement')->first();

            if ($bestImprovement && $bestImprovement['improvement'] > 0) {
                // Tambahkan interpretasi tentang program studi dengan peningkatan terbesar
                $interpretation[] = "Peningkatan skor rata-rata terbesar terjadi pada program studi **{$bestImprovement['program']}** dengan kenaikan sebesar **" . number_format($bestImprovement['improvement'], 1) . " poin**.";
            }
        }

        // Kembalikan data perbandingan dan interpretasi
        return [
            'data' => $scoreComparisonData,
            'interpretation' => $interpretation,
        ];
    }
}
