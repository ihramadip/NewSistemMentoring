<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\User;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        // 5. Granular Level Progression Analysis
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
        
        $levelMapping = $levels->pluck('id')->sort()->values()->all(); // [1, 2, 3, 4]
        $getFinalLevelId = function($score) use ($levelMapping) {
            if ($score <= 40) return $levelMapping[0] ?? 1;
            if ($score <= 60) return $levelMapping[1] ?? 2;
            if ($score <= 80) return $levelMapping[2] ?? 3;
            return $levelMapping[3] ?? 4;
        };
        
        $levelProgressionByFacultyAndLevel = [];
        // Initialize the structure
        foreach ($allFaculties as $faculty) {
            foreach($levels as $level) {
                $levelProgressionByFacultyAndLevel[$faculty][$level->name] = ['up' => 0, 'down' => 0, 'same' => 0];
            }
        }

        // Populate the structure
        foreach ($levelProgressionData as $data) {
            $finalLevelId = $getFinalLevelId($data->final_exam_score);
            $initialLevelId = $data->initial_level_id;
            $facultyName = $data->faculty_name;
            $initialLevelName = $data->initial_level_name;

            if (!isset($levelProgressionByFacultyAndLevel[$facultyName][$initialLevelName])) continue;

            if ($finalLevelId > $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$facultyName][$initialLevelName]['up']++;
            } elseif ($finalLevelId < $initialLevelId) {
                $levelProgressionByFacultyAndLevel[$facultyName][$initialLevelName]['down']++;
            } else {
                $levelProgressionByFacultyAndLevel[$facultyName][$initialLevelName]['same']++;
            }
        }

        // 6. Score Progression Analysis (Individual Mentee Score Comparison)
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
            $facultyName = $data->faculty_name;
            $placementScore = $data->placement_avg_score;
            $finalExamScore = $data->final_exam_score;

            if (!isset($scoreProgressionAnalysis[$facultyName])) continue;

            if ($finalExamScore > $placementScore) {
                $scoreProgressionAnalysis[$facultyName]['up']++;
            } elseif ($finalExamScore < $placementScore) {
                $scoreProgressionAnalysis[$facultyName]['down']++;
            } else {
                $scoreProgressionAnalysis[$facultyName]['same']++;
            }
        }


        // 7. Attendance Statistics
        $totalMenteesByFaculty = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->where('users.role_id', 3) // Assuming role_id 3 is Mentee
            ->select('faculties.name as faculty_name', DB::raw('COUNT(users.id) as total_mentees'))
            ->groupBy('faculties.name')
            ->pluck('total_mentees', 'faculty_name');

        $placementTestAttendance = DB::table('placement_tests')
            ->join('users', 'placement_tests.mentee_id', '=', 'users.id')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name as faculty_name', DB::raw('COUNT(DISTINCT users.id) as attended_count'))
            ->groupBy('faculties.name')
            ->pluck('attended_count', 'faculty_name');

        $finalExamAttendance = DB::table('exam_submissions')
            ->join('users', 'exam_submissions.mentee_id', '=', 'users.id')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name as faculty_name', DB::raw('COUNT(DISTINCT users.id) as attended_count'))
            ->groupBy('faculties.name')
            ->pluck('attended_count', 'faculty_name');
        
        $attendanceStats = [];
        foreach ($totalMenteesByFaculty as $faculty => $total) {
            $placementCount = $placementTestAttendance[$faculty] ?? 0;
            $finalExamCount = $finalExamAttendance[$faculty] ?? 0;
            $attendanceStats[$faculty] = [
                'total_mentees' => $total,
                'placement_attended' => $placementCount,
                'placement_percentage' => $total > 0 ? ($placementCount / $total) * 100 : 0,
                'final_exam_attended' => $finalExamCount,
                'final_exam_percentage' => $total > 0 ? ($finalExamCount / $total) * 100 : 0,
            ];
        }

        // 8. Individual Analysis Table with Search
        $query = DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->where('users.role_id', 3) // Mentee
            ->where('exam_submissions.status', 'graded')
            ->whereNotNull('placement_tests.audio_reading_score')
            ->whereNotNull('placement_tests.theory_score')
            ->select(
                'users.npm',
                'users.name',
                DB::raw('ROUND((placement_tests.audio_reading_score + placement_tests.theory_score) / 2, 2) as placement_score'),
                'exam_submissions.total_score as final_exam_score'
            );

        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('users.name', 'like', $searchTerm)
                  ->orWhere('users.npm', 'like', 'searchTerm');
            });
        }
        
        $individualAnalyses = $query->orderBy('users.npm')->paginate(10)->withQueryString();


        return view('admin.statistics.index', compact(
            'facultyStats', 
            'programStats', 
            'levelDistribution', 
            'levels', 
            'scoreComparisonData', 
            'levelProgressionByFacultyAndLevel', 
            'scoreProgressionAnalysis', 
            'attendanceStats',
            'individualAnalyses'
        ));
    }
}
