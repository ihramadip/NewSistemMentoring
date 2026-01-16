<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProgressionStatisticService
{
    /**
     * Get score and level progression analysis.
     *
     * @param Collection $levels
     * @param Collection $allFaculties
     * @return array
     */
    public function getAnalysis(Collection $levels, Collection $allFaculties): array
    {
        // Part 1: Level Progression Analysis
        $levelProgressionResult = $this->getLevelProgression($levels, $allFaculties);

        // Part 2: Score Progression Analysis
        $scoreProgressionResult = $this->getScoreProgression($allFaculties);

        return array_merge($levelProgressionResult, $scoreProgressionResult);
    }

    /**
     * Calculates the progression of mentees between levels.
     *
     * @param Collection $levels
     * @param Collection $allFaculties
     * @return array
     */
    private function getLevelProgression(Collection $levels, Collection $allFaculties): array
    {
        $levelProgressionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->join('levels as initial_levels', 'placement_tests.final_level_id', '=', 'initial_levels.id')
            ->whereNotNull('placement_tests.final_level_id')
            ->where('exam_submissions.status', 'graded')
            ->select(
                'faculties.name as faculty_name',
                'initial_levels.id as initial_level_id',
                'initial_levels.name as initial_level_name',
                'exam_submissions.total_score as final_exam_score'
            )
            ->get();
        
        $levelMapping = $levels->pluck('id')->sort()->values()->toArray();
        $getFinalLevelId = function($score) use ($levelMapping) {
            if ($score <= 40) return $levelMapping[0] ?? 1;
            if ($score <= 60) return $levelMapping[1] ?? 2;
            if ($score <= 80) return $levelMapping[2] ?? 3;
            return $levelMapping[3] ?? 4;
        };
        
        $levelProgressionByFacultyAndLevel = [];
        foreach ($allFaculties as $faculty) {
            foreach($levels as $level) {
                $levelProgressionByFacultyAndLevel[$faculty][$level->name] = ['up' => 0, 'down' => 0, 'same' => 0];
            }
        }

        foreach ($levelProgressionData as $data) {
            $finalLevelId = $getFinalLevelId($data->final_exam_score);
            $initialLevelId = $data->initial_level_id;
            if (!isset($levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name])) continue;

            if ($finalLevelId > $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['up']++;
            } elseif ($finalLevelId < $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['down']++;
            } else {
                $levelProgressionByFacultyAndLevel[$data->faculty_name][$data->initial_level_name]['same']++;
            }
        }

        // Interpretations
        $levelProgressionByFacultyInterpretation = [];
        $highestLevelName = $levels->last()->name ?? null;
        $lowestLevelName = $levels->first()->name ?? null;

        foreach ($levelProgressionByFacultyAndLevel as $facultyName => $progressionData) {
            $totalUp = collect($progressionData)->sum('up');
            $totalDown = collect($progressionData)->sum('down');
            $totalSame = collect($progressionData)->sum('same');
            $totalMentees = $totalUp + $totalDown + $totalSame;

            if ($totalMentees > 0) {
                $levelWithMostUp = ['name' => null, 'count' => 0];
                $levelWithMostSame = ['name' => null, 'count' => 0];
                $levelWithMostDown = ['name' => null, 'count' => 0];

                foreach ($progressionData as $levelName => $stats) {
                    if ($stats['up'] > $levelWithMostUp['count'] && $levelName !== $highestLevelName) {
                        $levelWithMostUp = ['name' => $levelName, 'count' => $stats['up']];
                    }
                    if ($stats['same'] > $levelWithMostSame['count']) {
                        $levelWithMostSame = ['name' => $levelName, 'count' => $stats['same']];
                    }
                    if ($stats['down'] > $levelWithMostDown['count'] && $levelName !== $lowestLevelName) {
                        $levelWithMostDown = ['name' => $levelName, 'count' => $stats['down']];
                    }
                }

                $interpretationParts = ["Untuk fakultas **{$facultyName}**, dari **{$totalMentees}** pergerakan level mentee, **{$totalUp}** orang naik level, **{$totalSame}** tetap, dan **{$totalDown}** turun."];
                if ($levelWithMostUp['count'] > 0) $interpretationParts[] = "Kenaikan paling signifikan berasal dari level **{$levelWithMostUp['name']}** (**{$levelWithMostUp['count']}** orang).";
                if ($levelWithMostSame['count'] > 0) $interpretationParts[] = "Level **{$levelWithMostSame['name']}** adalah yang paling banyak membuat mentee-nya stagnan (**{$levelWithMostSame['count']}** orang).";
                if ($levelWithMostDown['count'] > 0) $interpretationParts[] = "Penurunan terbanyak dialami mentee dari level **{$levelWithMostDown['name']}** (**{$levelWithMostDown['count']}** orang).";
                $levelProgressionByFacultyInterpretation[$facultyName] = implode(' ', $interpretationParts);
            } else {
                $levelProgressionByFacultyInterpretation[$facultyName] = "Tidak ada data progresi level yang cukup untuk fakultas {$facultyName}.";
            }
        }
        
        $totalPromoted = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('up'));
        $totalDemoted = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('down'));
        $totalStayed = collect($levelProgressionByFacultyAndLevel)->sum(fn($faculty) => collect($faculty)->sum('same'));
        $totalTransitions = $totalPromoted + $totalDemoted + $totalStayed;
        $levelProgressionInterpretation = [];
        if($totalTransitions > 0) {
            $levelProgressionInterpretation[] = "Secara keseluruhan, **" . number_format(($totalPromoted / $totalTransitions) * 100, 1) . "%** mentee berhasil naik level, sementara **" . number_format(($totalDemoted / $totalTransitions) * 100, 1) . "%** mengalami penurunan level.";
        }

        return compact(
            'levelProgressionByFacultyAndLevel',
            'levelProgressionByFacultyInterpretation',
            'levelProgressionInterpretation'
        );
    }

    /**
     * Calculates the progression of mentee scores.
     *
     * @param Collection $allFaculties
     * @return array
     */
    private function getScoreProgression(Collection $allFaculties): array
    {
        $scoreProgressionData = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->whereNotNull('placement_tests.audio_reading_score')
            ->whereNotNull('placement_tests.theory_score')
            ->where('exam_submissions.status', 'graded')
            ->select(
                'faculties.name as faculty_name',
                DB::raw('((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as placement_avg_score'),
                'exam_submissions.total_score as final_exam_score'
            )
            ->get();
        
        $scoreProgressionAnalysis = [];
        foreach ($allFaculties as $faculty) {
            $scoreProgressionAnalysis[$faculty] = ['up' => 0, 'down' => 0, 'same' => 0];
        }

        foreach ($scoreProgressionData as $data) {
            if (!isset($scoreProgressionAnalysis[$data->faculty_name])) continue;

            if ($data->final_exam_score > $data->placement_avg_score) {
                $scoreProgressionAnalysis[$data->faculty_name]['up']++;
            } elseif ($data->final_exam_score < $data->placement_avg_score) {
                $scoreProgressionAnalysis[$data->faculty_name]['down']++;
            } else {
                $scoreProgressionAnalysis[$data->faculty_name]['same']++;
            }
        }

        $interpretation = [];
        $totalUp = collect($scoreProgressionAnalysis)->sum('up');
        $totalDown = collect($scoreProgressionAnalysis)->sum('down');
        $totalSame = collect($scoreProgressionAnalysis)->sum('same');
        $totalMentees = $totalUp + $totalDown + $totalSame;

        if ($totalMentees > 0) {
            $interpretation[] = "Dari total **" . number_format($totalMentees) . " mentee**, sebanyak **" . number_format(($totalUp / $totalMentees) * 100, 1) . "%** mengalami kenaikan nilai, sementara **" . number_format(($totalDown / $totalMentees) * 100, 1) . "%** mengalami penurunan.";
        }

        $facultyMostImproved = collect($scoreProgressionAnalysis)->map(function ($stats, $faculty) {
            $total = $stats['up'] + $stats['down'] + $stats['same'];
            return ['faculty' => $faculty, 'percentage_up' => $total > 0 ? ($stats['up'] / $total) * 100 : 0];
        })->sortByDesc('percentage_up')->first();

        if ($facultyMostImproved) {
            $interpretation[] = "Fakultas **{$facultyMostImproved['faculty']}** menunjukkan persentase mentee yang nilainya naik paling tinggi, yaitu **" . number_format($facultyMostImproved['percentage_up'], 1) . "%** dari total mentee di fakultas tersebut.";
        }
        
        return [
            'scoreProgressionAnalysis' => $scoreProgressionAnalysis,
            'scoreProgressionInterpretation' => $interpretation,
        ];
    }
}
