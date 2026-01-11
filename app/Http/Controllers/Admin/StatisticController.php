<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Level;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Stats per Faculty
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

        // 2. Stats per Program Study
        $programStats = DB::table('users')
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
            
        // 3. Level Distribution per Faculty (Pivot Table)
        $levels = Level::orderBy('id')->get();
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
        // Initialize with all faculties and zero counts for all levels
        $allFaculties = DB::table('faculties')->orderBy('name')->pluck('name');
        foreach ($allFaculties as $faculty) {
            $levelDistribution[$faculty]['total'] = 0;
            foreach ($levels as $level) {
                $levelDistribution[$faculty][$level->name] = 0;
            }
        }

        // Populate with actual data
        foreach ($levelDistributionData as $row) {
            if (isset($levelDistribution[$row->faculty_name][$row->level_name])) {
                $levelDistribution[$row->faculty_name][$row->level_name] = $row->mentee_count;
                $levelDistribution[$row->faculty_name]['total'] += $row->mentee_count;
            }
        }

        // 4. Score Comparison Data for Chart
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


        return view('admin.statistics.index', compact('facultyStats', 'programStats', 'levelDistribution', 'levels', 'scoreComparisonData'));
    }
}
