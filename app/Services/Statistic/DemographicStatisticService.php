<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;

class DemographicStatisticService
{
    /**
     * Get statistics per faculty including mentee count and average scores.
     *
     * @return array
     */
    public function getFacultyStats(): array
    {
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

        $interpretation = [];
        if ($facultyStats->isNotEmpty()) {
            $topFacultyMentee = $facultyStats->sortByDesc('mentee_count')->first();
            $interpretation[] = "Fakultas dengan partisipasi mentee terbanyak adalah **{$topFacultyMentee->faculty_name}** (" . number_format($topFacultyMentee->mentee_count) . " orang).";
            
            $facultyWithHighestScore = $facultyStats->map(function ($f) {
                $f->combined_score = ($f->avg_audio_score + $f->avg_theory_score) / 2;
                return $f;
            })->sortByDesc('combined_score')->first();
            $interpretation[] = "Dari segi performa, **{$facultyWithHighestScore->faculty_name}** menunjukkan skor rata-rata Placement Test tertinggi (**" . number_format($facultyWithHighestScore->combined_score, 1) . "**).";
        }

        return [
            'stats' => $facultyStats,
            'interpretation' => $interpretation,
        ];
    }

    /**
     * Get statistics per program study including mentee count and average scores.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProgramStats(): \Illuminate\Support\Collection
    {
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
