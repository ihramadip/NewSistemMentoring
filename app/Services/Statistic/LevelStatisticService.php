<?php

namespace App\Services\Statistic;

use App\Models\Level;
use Illuminate\Support\Facades\DB;

class LevelStatisticService
{
    /**
     * Get level distribution statistics per faculty.
     *
     * @return array
     */
    public function getDistribution(): array
    {
        $levels = Level::orderBy('id')->get();
        $levelNames = $levels->pluck('name')->toArray();
        
        $levelDistributionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('levels', 'placement_tests.final_level_id', '=', 'levels.id')
            ->select(
                'faculties.name as faculty_name',
                'levels.name as level_name',
                DB::raw('COUNT(users.id) as mentee_count')
            )
            ->whereNotNull('placement_tests.final_level_id')
            ->groupBy('faculties.name', 'levels.name')
            ->orderBy('faculties.name')
            ->get();

        $levelDistribution = [];
        $allFaculties = DB::table('faculties')->orderBy('name')->pluck('name');
        foreach ($allFaculties as $faculty) {
            $levelDistribution[$faculty]['total'] = 0;
            foreach ($levels as $level) {
                $levelDistribution[$faculty][$level->name] = 0;
            }
        }

        foreach ($levelDistributionData as $row) {
            if (isset($levelDistribution[$row->faculty_name][$row->level_name])) {
                $levelDistribution[$row->faculty_name][$row->level_name] = $row->mentee_count;
                $levelDistribution[$row->faculty_name]['total'] += $row->mentee_count;
            }
        }

        $interpretation = [];
        if (!empty($levelDistribution)) {
            $levelTotals = [];
            foreach ($levelDistribution as $facultyData) {
                foreach ($levels as $level) {
                    $levelTotals[$level->name] = ($levelTotals[$level->name] ?? 0) + $facultyData[$level->name];
                }
            }
            
            // Only generate interpretation if there is actual data to interpret.
            $totalMenteesInLevels = array_sum($levelTotals);
            if ($totalMenteesInLevels > 0) {
                arsort($levelTotals);
                $mostCommonLevel = key($levelTotals);
                $interpretation[] = "Secara keseluruhan, level awal yang paling banyak ditempati mentee adalah **{$mostCommonLevel}**, menandakan ini sebagai titik awal mayoritas peserta.";

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

        $effectivenessData = $this->getLevelEffectivenessData($levels->all());

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
     * Get data for level effectiveness analysis.
     *
     * @param array $levels
     * @return array
     */
    public function getLevelEffectivenessData(array $levels): array
    {
        $levelOrder = array_column($levels, 'id');
        
        $caseClauses = "";
        foreach ($levels as $index => $level) {
            if ($index == 0) {
                $caseClauses .= "WHEN es.total_score <= 40 THEN " . $level['id'] . " ";
            } elseif ($index == 1) {
                $caseClauses .= "WHEN es.total_score <= 60 THEN " . $level['id'] . " ";
            } elseif ($index == 2) {
                $caseClauses .= "WHEN es.total_score <= 80 THEN " . $level['id'] . " ";
            } else {
                $caseClauses .= "ELSE " . $level['id'] . " ";
            }
        }
        
        $transitions = DB::table('users as u')
            ->join('placement_tests as pt', 'u.id', '=', 'pt.mentee_id')
            ->join('exam_submissions as es', 'u.id', '=', 'es.mentee_id')
            ->join('levels as initial_level', 'pt.final_level_id', '=', 'initial_level.id')
            ->where('es.status', 'graded')
            ->whereNotNull('pt.final_level_id')
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
                $finalLevel = collect($levels)->firstWhere('id', $row->final_level_id);
                return [
                    'initial' => $row->initial_level_name,
                    'final' => data_get($finalLevel, 'name', 'Unknown'),
                ];
            });

        $levelEffectivenessMatrix = [];
        $levelTotals = [];
        $levelNames = array_column($levels, 'name');

        foreach ($levelNames as $initialLevelName) {
            $levelTotals[$initialLevelName] = 0;
            foreach ($levelNames as $finalLevelName) {
                $levelEffectivenessMatrix[$initialLevelName][$finalLevelName] = 0;
            }
        }

        foreach ($transitions as $transition) {
            if (isset($levelEffectivenessMatrix[$transition['initial']][$transition['final']])) {
                $levelEffectivenessMatrix[$transition['initial']][$transition['final']]++;
                $levelTotals[$transition['initial']]++;
            }
        }

        foreach ($levelEffectivenessMatrix as $initialLevelName => $finalLevels) {
            foreach ($finalLevels as $finalLevelName => $count) {
                if ($levelTotals[$initialLevelName] > 0) {
                    $levelEffectivenessMatrix[$initialLevelName][$finalLevelName] = ($count / $levelTotals[$initialLevelName]) * 100;
                }
            }
        }

        $levelEffectivenessInterpretation = [];
        $levelNameOrder = array_column($levels, 'name');

        foreach ($levelEffectivenessMatrix as $initialLevelName => $finalLevels) {
            $retentionRate = $finalLevels[$initialLevelName] ?? 0;
            $currentLevelIndex = array_search($initialLevelName, $levelNameOrder);
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

        return compact('levelEffectivenessMatrix', 'levelEffectivenessInterpretation');
    }
}
