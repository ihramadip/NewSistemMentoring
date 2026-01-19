<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * ProgressionStatisticService
 *
 * Service untuk mengelola statistik progresi mentoring (MODULE D: Admin Dashboard)
 * Menyediakan data analisis progresi level dan skor mentee berdasarkan fakultas
 *
 * Fungsi utama:
 * - getAnalysis(): Menggabungkan analisis progresi level dan skor
 * - getLevelProgression(): Menghitung progresi level mentee
 * - getScoreProgression(): Menghitung progresi skor mentee
 *
 * Data yang dihasilkan:
 * - Progresi level per fakultas dan level
 * - Interpretasi progresi level
 * - Progresi skor per fakultas
 * - Interpretasi progresi skor
 *
 * @package App\Services\Statistic
 */
class ProgressionStatisticService
{
    /**
     * Mengembalikan analisis progresi skor dan level mentee
     *
     * Proses:
     * 1. Ambil analisis progresi level
     * 2. Ambil analisis progresi skor
     * 3. Gabungkan kedua analisis dan kembalikan
     *
     * @param Collection $levels Koleksi objek level
     * @param Collection $allFaculties Koleksi nama fakultas
     * @return array Array berisi data progresi level dan skor
     */
    public function getAnalysis(Collection $levels, Collection $allFaculties): array
    {
        // Bagian 1: Analisis Progresi Level
        $levelProgressionResult = $this->getLevelProgression($levels, $allFaculties);

        // Bagian 2: Analisis Progresi Skor
        $scoreProgressionResult = $this->getScoreProgression($allFaculties);

        // Gabungkan hasil kedua analisis dan kembalikan
        return array_merge($levelProgressionResult, $scoreProgressionResult);
    }

    /**
     * Menghitung progresi mentee antar level
     *
     * Proses:
     * 1. Ambil data progresi level dari database
     * 2. Tentukan level akhir berdasarkan skor ujian akhir
     * 3. Bangun struktur data progresi level
     * 4. Isi data progresi berdasarkan perbandingan level awal dan akhir
     * 5. Buat interpretasi berdasarkan data progresi
     * 6. Kembalikan data progresi level dan interpretasi
     *
     * @param Collection $levels Koleksi objek level
     * @param Collection $allFaculties Koleksi nama fakultas
     * @return array Array berisi data progresi level dan interpretasi
     */
    private function getLevelProgression(Collection $levels, Collection $allFaculties): array
    {
        // Ambil data progresi level dari database
        $levelProgressionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->join('levels as initial_levels', 'placement_tests.final_level_id', '=', 'initial_levels.id')
            ->whereNotNull('placement_tests.final_level_id') // Hanya ambil data dengan level awal yang ditentukan
            ->where('exam_submissions.status', 'graded') // Hanya ambil ujian akhir yang sudah dinilai
            ->select(
                'faculties.name as faculty_name',
                'initial_levels.id as initial_level_id',
                'initial_levels.name as initial_level_name',
                'exam_submissions.total_score as final_exam_score'
            )
            ->get();

        // Bangun mapping ID level dan fungsi untuk menentukan level akhir berdasarkan skor
        $levelMapping = $levels->pluck('id')->sort()->values()->toArray();
        $getFinalLevelId = function($score) use ($levelMapping) {
            if ($score <= 40) return $levelMapping[0] ?? 1;
            if ($score <= 60) return $levelMapping[1] ?? 2;
            if ($score <= 80) return $levelMapping[2] ?? 3;
            return $levelMapping[3] ?? 4;
        };

        // Bangun struktur data progresi level
        $levelProgressionByFacultyAndLevel = [];
        foreach ($allFaculties as $faculty) {
            foreach($levels as $level) {
                $levelProgressionByFacultyAndLevel[$faculty][$level->name] = ['up' => 0, 'down' => 0, 'same' => 0]; // Naik, turun, tetap
            }
        }

        // Isi data progresi berdasarkan perbandingan level awal dan akhir
        foreach ($levelProgressionData as $data) {
            $finalLevelId = $getFinalLevelId($data->final_exam_score); // Tentukan level akhir berdasarkan skor
            $initialLevelId = $data->initial_level_id; // Level awal dari placement test

            // Lewati jika fakultas atau level tidak valid
            if (!isset($levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name])) continue;

            // Tentukan apakah mentee naik, turun, atau tetap level
            if ($finalLevelId > $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['up']++; // Naik level
            } elseif ($finalLevelId < $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['down']++; // Turun level
            } else {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['same']++; // Tetap level
            }
        }

        // Buat interpretasi berdasarkan data progresi level
        $levelProgressionByFacultyInterpretation = [];
        $highestLevelName = $levels->last()->name ?? null; // Nama level tertinggi
        $lowestLevelName = $levels->first()->name ?? null; // Nama level terendah

        foreach ($levelProgressionByFacultyAndLevel as $facultyName => $progressionData) {
            // Hitung total pergerakan level untuk fakultas ini
            $totalUp = collect($progressionData)->sum('up');
            $totalDown = collect($progressionData)->sum('down');
            $totalSame = collect($progressionData)->sum('same');
            $totalMentees = $totalUp + $totalDown + $totalSame;

            if ($totalMentees > 0) {
                // Temukan level dengan jumlah pergerakan tertinggi untuk setiap jenis pergerakan
                $levelWithMostUp = ['name' => null, 'count' => 0];
                $levelWithMostSame = ['name' => null, 'count' => 0];
                $levelWithMostDown = ['name' => null, 'count' => 0];

                foreach ($progressionData as $levelName => $stats) {
                    // Temukan level dengan kenaikan tertinggi (kecuali level tertinggi)
                    if ($stats['up'] > $levelWithMostUp['count'] && $levelName !== $highestLevelName) {
                        $levelWithMostUp = ['name' => $levelName, 'count' => $stats['up']];
                    }
                    // Temukan level dengan stagnasi tertinggi
                    if ($stats['same'] > $levelWithMostSame['count']) {
                        $levelWithMostSame = ['name' => $levelName, 'count' => $stats['same']];
                    }
                    // Temukan level dengan penurunan tertinggi (kecuali level terendah)
                    if ($stats['down'] > $levelWithMostDown['count'] && $levelName !== $lowestLevelName) {
                        $levelWithMostDown = ['name' => $levelName, 'count' => $stats['down']];
                    }
                }

                // Bangun teks interpretasi untuk fakultas ini
                $interpretationParts = ["Untuk fakultas **{$facultyName}**, dari **{$totalMentees}** pergerakan level mentee, **{$totalUp}** orang naik level, **{$totalSame}** tetap, dan **{$totalDown}** turun."];
                if ($levelWithMostUp['count'] > 0) $interpretationParts[] = "Kenaikan paling signifikan berasal dari level **{$levelWithMostUp['name']}** (**{$levelWithMostUp['count']}** orang).";
                if ($levelWithMostSame['count'] > 0) $interpretationParts[] = "Level **{$levelWithMostSame['name']}** adalah yang paling banyak membuat mentee-nya stagnan (**{$levelWithMostSame['count']}** orang).";
                if ($levelWithMostDown['count'] > 0) $interpretationParts[] = "Penurunan terbanyak dialami mentee dari level **{$levelWithMostDown['name']}** (**{$levelWithMostDown['count']}** orang).";
                $levelProgressionByFacultyInterpretation[$facultyName] = implode(' ', $interpretationParts);
            } else {
                $levelProgressionByFacultyInterpretation[$facultyName] = "Tidak ada data progresi level yang cukup untuk fakultas {$facultyName}.";
            }
        }

        // Hitung total pergerakan level secara keseluruhan
        $totalPromoted = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('up'));
        $totalDemoted = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('down'));
        $totalStayed = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('same'));
        $totalTransitions = $totalPromoted + $totalDemoted + $totalStayed;

        // Buat interpretasi keseluruhan
        $levelProgressionInterpretation = [];
        if($totalTransitions > 0) {
            $levelProgressionInterpretation[] = "Secara keseluruhan, **" . number_format(($totalPromoted / $totalTransitions) * 100, 1) . "%** mentee berhasil naik level, sementara **" . number_format(($totalDemoted / $totalTransitions) * 100, 1) . "%** mengalami penurunan level.";
        }

        // Kembalikan data progresi level dan interpretasi
        return compact(
            'levelProgressionByFacultyAndLevel',
            'levelProgressionByFacultyInterpretation',
            'levelProgressionInterpretation'
        );
    }

    /**
     * Menghitung progresi skor mentee
     *
     * Proses:
     * 1. Ambil data progresi skor dari database
     * 2. Bangun struktur data progresi skor
     * 3. Isi data progresi berdasarkan perbandingan skor placement test dan ujian akhir
     * 4. Buat interpretasi berdasarkan data progresi skor
     * 5. Kembalikan data progresi skor dan interpretasi
     *
     * @param Collection $allFaculties Koleksi nama fakultas
     * @return array Array berisi data progresi skor dan interpretasi
     */
    private function getScoreProgression(Collection $allFaculties): array
    {
        // Ambil data progresi skor dari database
        $scoreProgressionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->whereNotNull('placement_tests.audio_reading_score') // Pastikan skor placement test tersedia
            ->whereNotNull('placement_tests.theory_score') // Pastikan skor theory test tersedia
            ->where('exam_submissions.status', 'graded') // Hanya ambil ujian akhir yang sudah dinilai
            ->select(
                'faculties.name as faculty_name',
                DB::raw('((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as placement_avg_score'), // Skor rata-rata placement test
                'exam_submissions.total_score as final_exam_score' // Skor ujian akhir
            )
            ->get();

        // Bangun struktur data progresi skor
        $scoreProgressionAnalysis = [];
        foreach ($allFaculties as $faculty) {
            $scoreProgressionAnalysis[$faculty] = ['up' => 0, 'down' => 0, 'same' => 0]; // Naik, turun, tetap
        }

        // Isi data progresi berdasarkan perbandingan skor placement test dan ujian akhir
        foreach ($scoreProgressionData as $data) {
            // Lewati jika fakultas tidak valid
            if (!isset($scoreProgressionAnalysis[$data->faculty_name])) continue;

            // Bandingkan skor placement test dan ujian akhir
            if ($data->final_exam_score > $data->placement_avg_score) {
                $scoreProgressionAnalysis[$data->faculty_name]['up']++; // Skor naik
            } elseif ($data->final_exam_score < $data->placement_avg_score) {
                $scoreProgressionAnalysis[$data->faculty_name]['down']++; // Skor turun
            } else {
                $scoreProgressionAnalysis[$data->faculty_name]['same']++; // Skor tetap
            }
        }

        // Buat interpretasi berdasarkan data progresi skor
        $interpretation = [];
        $totalUp = collect($scoreProgressionAnalysis)->sum('up');
        $totalDown = collect($scoreProgressionAnalysis)->sum('down');
        $totalSame = collect($scoreProgressionAnalysis)->sum('same');
        $totalMentees = $totalUp + $totalDown + $totalSame;

        if ($totalMentees > 0) {
            $interpretation[] = "Dari total **" . number_format($totalMentees) . " mentee**, sebanyak **" . number_format(($totalUp / $totalMentees) * 100, 1) . "%** mengalami kenaikan nilai, sementara **" . number_format(($totalDown / $totalMentees) * 100, 1) . "%** mengalami penurunan.";
        }

        // Temukan fakultas dengan persentase kenaikan skor tertinggi
        $facultyMostImproved = collect($scoreProgressionAnalysis)->map(function ($stats, $faculty) {
            $total = $stats['up'] + $stats['down'] + $stats['same'];
            return ['faculty' => $faculty, 'percentage_up' => $total > 0 ? ($stats['up'] / $total) * 100 : 0];
        })->sortByDesc('percentage_up')->first();

        if ($facultyMostImproved) {
            $interpretation[] = "Fakultas **{$facultyMostImproved['faculty']}** menunjukkan persentase mentee yang nilainya naik paling tinggi, yaitu **" . number_format($facultyMostImproved['percentage_up'], 1) . "%** dari total mentee di fakultas tersebut.";
        }

        // Kembalikan data progresi skor dan interpretasi
        return [
            'scoreProgressionAnalysis' => $scoreProgressionAnalysis,
            'scoreProgressionInterpretation' => $interpretation,
        ];
    }
}
