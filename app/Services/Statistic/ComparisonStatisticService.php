<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;

class ComparisonStatisticService
{
    /**
     * Get score comparison data and interpretation between placement tests and final exams.
     *
     * @return array
     */
    public function getScoreComparison(): array
    {
        $placementScores = DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->whereNotNull('users.program_study')
            ->select(
                'users.program_study',
                DB::raw('AVG((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as avg_score')
            )
            ->groupBy('users.program_study')
            ->pluck('avg_score', 'program_study');

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
        
        $allPrograms = $placementScores->keys()->merge($finalExamScores->keys())->unique()->sort();
        $scoreComparisonData = [];
        foreach($allPrograms as $program) {
            $scoreComparisonData[$program] = [
                'placement' => $placementScores[$program] ?? 0,
                'final_exam' => $finalExamScores[$program] ?? 0,
            ];
        }

        $interpretation = [];
        if (!empty($scoreComparisonData)) {
            $allProgramsData = collect($scoreComparisonData);
            $avgPlacement = $allProgramsData->avg('placement');
            $avgFinal = $allProgramsData->avg('final_exam');

            if ($avgFinal > $avgPlacement) {
                $interpretation[] = "Secara umum, terdapat peningkatan performa mentee dengan rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih tinggi dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**).";
            } else {
                $interpretation[] = "Perlu diperhatikan, rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih rendah dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**) secara keseluruhan.";
            }

            $bestImprovement = $allProgramsData->map(function ($scores, $program) {
                return ['program' => $program, 'improvement' => $scores['final_exam'] - $scores['placement']];
            })->sortByDesc('improvement')->first();

            if ($bestImprovement && $bestImprovement['improvement'] > 0) {
                $interpretation[] = "Peningkatan skor rata-rata terbesar terjadi pada program studi **{$bestImprovement['program']}** dengan kenaikan sebesar **" . number_format($bestImprovement['improvement'], 1) . " poin**.";
            }
        }

        return [
            'data' => $scoreComparisonData,
            'interpretation' => $interpretation,
        ];
    }
}
