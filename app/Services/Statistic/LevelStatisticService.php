<?php

namespace App\Services\Statistic;

use App\Models\Level;
use Illuminate\Support\Facades\DB;

/**
 * LevelStatisticService
 *
 * Service untuk mengelola statistik distribusi level mentoring (MODULE D: Admin Dashboard)
 * Menyediakan data distribusi mentee berdasarkan level dan fakultas serta analisis efektivitas level
 *
 * Fungsi utama:
 * - getDistribution(): Menghitung distribusi mentee berdasarkan level dan fakultas
 * - getLevelEffectivenessData(): Menghitung data efektivitas perpindahan level
 *
 * Data yang dihasilkan:
 * - Distribusi mentee per level dan fakultas
 * - Interpretasi distribusi level
 * - Matriks efektivitas level
 * - Interpretasi efektivitas level
 *
 * @package App\Services\Statistic
 */
class LevelStatisticService
{
    /**
     * Menghitung dan mengembalikan statistik distribusi level per fakultas
     *
     * Proses:
     * 1. Ambil semua data level dari model Level
     * 2. Ambil data distribusi mentee berdasarkan level dan fakultas dari database
     * 3. Bangun struktur data distribusi level
     * 4. Isi data distribusi berdasarkan hasil query
     * 5. Buat interpretasi berdasarkan data distribusi
     * 6. Ambil data efektivitas level
     * 7. Gabungkan semua data dan kembalikan sebagai array
     *
     * @return array Array berisi data distribusi level, interpretasi, dan data efektivitas
     */
    public function getDistribution(): array
    {
        // Ambil semua level dari model Level dan ekstrak nama level
        $levels = Level::orderBy('id')->get();
        $levelNames = $levels->pluck('name')->toArray();

        // Ambil data distribusi mentee berdasarkan fakultas dan level dari database
        $levelDistributionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('levels', 'placement_tests.final_level_id', '=', 'levels.id')
            ->select(
                'faculties.name as faculty_name',
                'levels.name as level_name',
                DB::raw('COUNT(users.id) as mentee_count')
            )
            ->whereNotNull('placement_tests.final_level_id') // Hanya ambil data dengan level final yang ditentukan
            ->groupBy('faculties.name', 'levels.name')
            ->orderBy('faculties.name')
            ->get();

        // Bangun struktur data distribusi level
        $levelDistribution = [];
        $allFaculties = DB::table('faculties')->orderBy('name')->pluck('name');
        foreach ($allFaculties as $faculty) {
            $levelDistribution[$faculty]['total'] = 0; // Total mentee per fakultas
            foreach ($levels as $level) {
                $levelDistribution[$faculty][$level->name] = 0; // Jumlah mentee per level dalam fakultas
            }
        }

        // Isi data distribusi berdasarkan hasil query
        foreach ($levelDistributionData as $row) {
            if (isset($levelDistribution[$row->faculty_name][$row->level_name])) {
                $levelDistribution[$row->faculty_name][$row->level_name] = $row->mentee_count;
                $levelDistribution[$row->faculty_name]['total'] += $row->mentee_count;
            }
        }

        // Buat interpretasi berdasarkan data distribusi
        $interpretation = [];
        if (!empty($levelDistribution)) {
            // Hitung total mentee per level
            $levelTotals = [];
            foreach ($levelDistribution as $facultyData) {
                foreach ($levels as $level) {
                    $levelTotals[$level->name] = ($levelTotals[$level->name] ?? 0) + $facultyData[$level->name];
                }
            }

            // Hanya buat interpretasi jika ada data untuk diinterpretasikan
            $totalMenteesInLevels = array_sum($levelTotals);
            if ($totalMenteesInLevels > 0) {
                arsort($levelTotals);
                $mostCommonLevel = key($levelTotals);
                $interpretation[] = "Secara keseluruhan, level awal yang paling banyak ditempati mentee adalah **{$mostCommonLevel}**, menandakan ini sebagai titik awal mayoritas peserta.";

                // Temukan fakultas penyumbang mentee terbanyak untuk setiap level
                $topFacultyByLevel = [];
                foreach($levels as $level) {
                    $topFaculty = collect($levelDistribution)->map(function ($data, $faculty) use ($level) {
                        return ['faculty' => $faculty, 'count' => $data[$level->name] ?? 0];
                    })->sortByDesc('count')->first();

                    if ($topFaculty && $topFaculty['count'] > 0) {
                        $topFacultyByLevel[$level->name] = $topFaculty['faculty'];
                    }
                }

                if (!empty($topFacultyByLevel)) {
                    $interpretationText = "Fakultas penyumbang mentee terbanyak untuk level ";
                    $parts = [];
                    foreach($topFacultyByLevel as $levelName => $facultyName) {
                        $parts[] = "**{$levelName}** adalah **{$facultyName}**";
                    }
                    $interpretation[] = $interpretationText . implode(', ', $parts) . ".";
                }
            }
        }

        // Ambil data efektivitas level
        $effectivenessData = $this->getLevelEffectivenessData($levels->all());

        // Gabungkan semua data dan kembalikan
        return array_merge(
            [
                'levels' => $levels,
                'levelNames' => $levelNames,
                'levelDistribution' => $levelDistribution,
                'interpretation' => $interpretation,
            ],
            $effectivenessData
        );
    }

    /**
     * Menghitung dan mengembalikan data untuk analisis efektivitas level
     *
     * Proses:
     * 1. Bangun klausa CASE untuk menentukan level berdasarkan skor ujian akhir
     * 2. Ambil data transisi level dari placement test ke ujian akhir
     * 3. Bangun matriks efektivitas level
     * 4. Hitung persentase perpindahan antar level
     * 5. Buat interpretasi efektivitas level
     * 6. Kembalikan matriks efektivitas dan interpretasi
     *
     * @param array $levels Array objek level
     * @return array Array berisi matriks efektivitas dan interpretasi
     */
    public function getLevelEffectivenessData(array $levels): array
    {
        // Bangun klausa CASE untuk menentukan level berdasarkan skor ujian akhir
        $caseClauses = "";
        foreach ($levels as $index => $level) {
            if ($index == 0) {
                $caseClauses .= "WHEN es.total_score <= 40 THEN " . $level['id'] . " ";
            } elseif ($index == 1) {
                $caseClauses .= "WHEN es.total_score <= 60 THEN " . $level['id'] . " ";
            } elseif ($index == 2) {
                $caseClauses .= "WHEN es.total_score <= 80 THEN " . $level['id'] . " ";
            } else {
                $caseClauses .= "ELSE " . $level['id'] . " "; // Level tertinggi
            }
        }

        // Ambil data transisi level dari placement test ke ujian akhir
        $transitions = DB::table('users as u')
            ->join('placement_tests as pt', 'u.id', '=', 'pt.mentee_id')
            ->join('exam_submissions as es', 'u.id', '=', 'es.mentee_id')
            ->join('levels as initial_level', 'pt.final_level_id', '=', 'initial_level.id')
            ->where('es.status', 'graded') // Hanya ambil ujian akhir yang sudah dinilai
            ->whereNotNull('pt.final_level_id') // Hanya ambil data dengan level awal yang ditentukan
            ->select(
                'initial_level.name as initial_level_name',
                DB::raw("
                    (CASE
                        {$caseClauses}
                    END) as final_level_id
                ")
            )
            ->get()
            ->map(function ($row) use ($levels) {
                // Temukan level akhir berdasarkan ID
                $finalLevel = collect($levels)->firstWhere('id', $row->final_level_id);
                return [
                    'initial' => $row->initial_level_name, // Level awal
                    'final' => data_get($finalLevel, 'name', 'Unknown'), // Level akhir
                ];
            });

        // Bangun matriks efektivitas level
        $levelEffectivenessMatrix = [];
        $levelTotals = [];
        $levelNames = array_column($levels, 'name');

        // Inisialisasi matriks dan total per level awal
        foreach ($levelNames as $initialLevelName) {
            $levelTotals[$initialLevelName] = 0;
            foreach ($levelNames as $finalLevelName) {
                $levelEffectivenessMatrix[$initialLevelName][$finalLevelName] = 0;
            }
        }

        // Hitung jumlah transisi antar level
        foreach ($transitions as $transition) {
            if (isset($levelEffectivenessMatrix[$transition['initial']][$transition['final']])) {
                $levelEffectivenessMatrix[$transition['initial']][$transition['final']]++;
                $levelTotals[$transition['initial']]++;
            }
        }

        // Konversi jumlah transisi ke persentase
        foreach ($levelEffectivenessMatrix as $initialLevelName => $finalLevels) {
            foreach ($finalLevels as $finalLevelName => $count) {
                if ($levelTotals[$initialLevelName] > 0) {
                    $levelEffectivenessMatrix[$initialLevelName][$finalLevelName] = ($count / $levelTotals[$initialLevelName]) * 100;
                }
            }
        }

        // Buat interpretasi efektivitas level
        $levelEffectivenessInterpretation = [];
        $levelNameOrder = array_column($levels, 'name');

        foreach ($levelEffectivenessMatrix as $initialLevelName => $finalLevels) {
            // Hitung tingkat retensi (tetap di level yang sama)
            $retentionRate = $finalLevels[$initialLevelName] ?? 0;
            $currentLevelIndex = array_search($initialLevelName, $levelNameOrder);

            // Hitung tingkat promosi dan degradasi
            $promotionRate = 0;
            $demotionRate = 0;
            $promotionTargets = [];

            foreach ($finalLevels as $finalLevelName => $percentage) {
                $finalLevelIndex = array_search($finalLevelName, $levelNameOrder);
                if ($finalLevelIndex > $currentLevelIndex) {
                    $promotionRate += $percentage;
                    $promotionTargets[$finalLevelName] = $percentage;
                } elseif ($finalLevelIndex < $currentLevelIndex) {
                    $demotionRate += $percentage;
                }
            }

            // Bangun teks interpretasi untuk level awal ini
            $interpretation = "Nah, buat mentee yang mulai di level **{$initialLevelName}**: ";
            $interpretation .= "yang berhasil bertahan di level ini ada sekitar **" . number_format($retentionRate, 1) . "%**. ";

            if ($promotionRate > 0) {
                arsort($promotionTargets);
                $topTargetLevel = key($promotionTargets);
                $interpretation .= "Terus, yang berhasil naik level ada sekitar **" . number_format($promotionRate, 1) . "%**, kebanyakan dari mereka naik ke level **{$topTargetLevel}**. ";
            } else {
                $interpretation .= "Belum ada yang berhasil naik ke level berikutnya. ";
            }

            if ($demotionRate > 0) {
                $interpretation .= "Sayangnya, ada sekitar **" . number_format($demotionRate, 1) . "%** yang levelnya turun.";
            }

            $levelEffectivenessInterpretation[] = $interpretation;
        }

        // Kembalikan matriks efektivitas dan interpretasi
        return compact('levelEffectivenessMatrix', 'levelEffectivenessInterpretation');
    }
}
