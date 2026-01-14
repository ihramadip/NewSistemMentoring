<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'statistics.all.' . $request->get('search', '') . '.page.' . $request->get('page', 1);
        $cacheDuration = now()->addHours(6);

        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
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
            
            $levelMapping = $levels->pluck('id')->toArray();
            sort($levelMapping);
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
                    $levelProgressionByFacultyAndLevel[$faculty][$level['name']] = ['up' => 0, 'down' => 0, 'same' => 0];
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

            $levelProgressionByFacultyInterpretation = [];
            $allLevels = collect($levels);
            $highestLevelName = data_get($allLevels->last(), 'name');
            $lowestLevelName = data_get($allLevels->first(), 'name');

            foreach ($levelProgressionByFacultyAndLevel as $facultyName => $progressionData) {
                $totalUp = 0;
                $totalDown = 0;
                $totalSame = 0;
                
                $levelWithMostUp = ['name' => null, 'count' => 0];
                $levelWithMostSame = ['name' => null, 'count' => 0];
                $levelWithMostDown = ['name' => null, 'count' => 0];

                foreach ($progressionData as $levelName => $stats) {
                    $totalUp += $stats['up'];
                    $totalDown += $stats['down'];
                    $totalSame += $stats['same'];

                    // Find level with most promotions (but not the highest level itself)
                    if ($stats['up'] > $levelWithMostUp['count'] && $levelName !== $highestLevelName) {
                        $levelWithMostUp = ['name' => $levelName, 'count' => $stats['up']];
                    }

                    // Find level with most stagnations
                    if ($stats['same'] > $levelWithMostSame['count']) {
                        $levelWithMostSame = ['name' => $levelName, 'count' => $stats['same']];
                    }

                    // Find level with most demotions (but not the lowest level itself)
                    if ($stats['down'] > $levelWithMostDown['count'] && $levelName !== $lowestLevelName) {
                        $levelWithMostDown = ['name' => $levelName, 'count' => $stats['down']];
                    }
                }

                $totalMentees = $totalUp + $totalDown + $totalSame;
                $interpretationParts = [];
                if ($totalMentees > 0) {
                    $interpretationParts[] = "Untuk fakultas **{$facultyName}**, dari **{$totalMentees}** pergerakan level mentee, **{$totalUp}** orang naik level, **{$totalSame}** tetap, dan **{$totalDown}** turun.";
                    
                    if ($levelWithMostUp['count'] > 0) {
                        $interpretationParts[] = "Kenaikan paling signifikan berasal dari level **{$levelWithMostUp['name']}** (**{$levelWithMostUp['count']}** orang).";
                    }

                    if ($levelWithMostSame['count'] > 0) {
                        $interpretationParts[] = "Level **{$levelWithMostSame['name']}** adalah yang paling banyak membuat mentee-nya stagnan (**{$levelWithMostSame['count']}** orang).";
                    }

                    if ($levelWithMostDown['count'] > 0) {
                        $interpretationParts[] = "Penurunan terbanyak dialami mentee dari level **{$levelWithMostDown['name']}** (**{$levelWithMostDown['count']}** orang).";
                    }
                    $interpretation = implode(' ', $interpretationParts);

                } else {
                    $interpretation = "Tidak ada data progresi level yang cukup untuk fakultas {$facultyName}.";
                }
                $levelProgressionByFacultyInterpretation[$facultyName] = $interpretation;
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

            // 9. Mentor Activity Analysis
            $mentors = User::where('role_id', 2) // Role 2 for Mentor
                ->with(['mentoringGroupsAsMentor.sessions.attendances', 'mentoringGroupsAsMentor.sessions.progressReports', 'faculty', 'mentoringGroupsAsMentor.members'])
                ->get();

            $mentorStats = $mentors->map(function ($mentor) {
                $totalReportsFilled = 0;
                $totalPossibleAttendances = 0;
                $totalPresentAttendances = 0;

                foreach ($mentor->mentoringGroupsAsMentor as $group) {
                    foreach ($group->sessions as $session) {
                        $totalReportsFilled += $session->progressReports->count();
                        $totalPossibleAttendances += $group->members->count();
                        $totalPresentAttendances += $session->attendances->where('status', 'hadir')->count();
                    }
                }

                $avgAttendanceRate = $totalPossibleAttendances > 0 ? round(($totalPresentAttendances / $totalPossibleAttendances) * 100) : 0;

                return [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'faculty' => $mentor->faculty->name ?? 'N/A',
                    'groups_count' => $mentor->mentoringGroupsAsMentor->count(),
                    'reports_filled' => $totalReportsFilled,
                    'avg_attendance_rate' => $avgAttendanceRate,
                ];
            });

            $mostActiveMentors = $mentorStats->sortByDesc('reports_filled')->take(10);
            $mentorsNeedingAttention = $mentorStats->filter(function($stat) {
                return $stat['reports_filled'] < 2 || $stat['avg_attendance_rate'] < 50;
            })->sortBy('avg_attendance_rate');

            // 10. Group Performance Analysis
            $allGroupsData = DB::table('mentoring_groups')
                ->join('users as mentors', 'mentoring_groups.mentor_id', '=', 'mentors.id')
                ->join('group_members', 'mentoring_groups.id', '=', 'group_members.mentoring_group_id')
                ->join('users as mentees', 'group_members.mentee_id', '=', 'mentees.id')
                ->leftJoin('placement_tests', 'mentees.id', '=', 'placement_tests.mentee_id')
                ->leftJoin('exam_submissions', 'mentees.id', '=', 'exam_submissions.mentee_id')
                ->where('exam_submissions.status', 'graded')
                ->whereNotNull('placement_tests.audio_reading_score')
                ->whereNotNull('placement_tests.theory_score')
                ->select(
                    'mentoring_groups.id as group_id',
                    'mentoring_groups.name as group_name',
                    'mentors.name as mentor_name',
                    DB::raw('AVG((placement_tests.audio_reading_score + placement_tests.theory_score) / 2) as avg_placement_score'),
                    DB::raw('AVG(exam_submissions.total_score) as avg_final_exam_score')
                )
                ->groupBy('mentoring_groups.id', 'mentoring_groups.name', 'mentors.name')
                ->get();

            $groupPerformance = $allGroupsData->map(function ($group) {
                $placementScore = $group->avg_placement_score;
                $finalExamScore = $group->avg_final_exam_score;
                $pointIncrease = $finalExamScore - $placementScore;

                // Assign new properties
                $group->avg_score_increase_points = $pointIncrease;

                if ($placementScore > 0) {
                    $group->avg_score_increase_percentage = ($pointIncrease / $placementScore) * 100;
                } else {
                    $group->avg_score_increase_percentage = 0;
                }

                // Assign category based on point increase
                if ($pointIncrease > 15) {
                    $group->category = 'Progres Sangat Baik';
                } elseif ($pointIncrease > 5) {
                    $group->category = 'Progres Baik';
                } elseif ($pointIncrease >= -5) {
                    $group->category = 'Stagnan';
                } else {
                    $group->category = 'Perlu Perhatian';
                }

                return $group;
            });

            $progressiveGroups = $groupPerformance->sortByDesc('avg_score_increase_points')->take(10);
            $stagnantGroups = $groupPerformance->sortBy('avg_score_increase_points')->take(10);

            // Refactored Level Effectiveness Analysis
            $levelEffectivenessData = $this->getLevelEffectivenessData($levels->all());

            // 13. Generate specific interpretations for each table
            $facultyStatsInterpretation = [];
            if ($facultyStats->isNotEmpty()) {
                $topFacultyMentee = $facultyStats->sortByDesc('mentee_count')->first();
                $facultyStatsInterpretation[] = "Fakultas dengan partisipasi mentee terbanyak adalah **{$topFacultyMentee->faculty_name}** (" . number_format($topFacultyMentee->mentee_count) . " orang).";
                
                $facultyWithHighestScore = $facultyStats->map(function ($f) {
                    $f->combined_score = ($f->avg_audio_score + $f->avg_theory_score) / 2;
                    return $f;
                })->sortByDesc('combined_score')->first();
                $facultyStatsInterpretation[] = "Dari segi performa, **{$facultyWithHighestScore->faculty_name}** menunjukkan skor rata-rata Placement Test tertinggi (**" . number_format($facultyWithHighestScore->combined_score, 1) . "**).";
            }

            $levelDistributionInterpretation = [];
            if (!empty($levelDistribution)) {
                $levelTotals = [];
                foreach ($levelDistribution as $facultyData) {
                    foreach ($levels as $level) {
                        $levelTotals[$level->name] = ($levelTotals[$level->name] ?? 0) + $facultyData[$level->name];
                    }
                }
                arsort($levelTotals);
                $mostCommonLevel = key($levelTotals);
                $levelDistributionInterpretation[] = "Secara keseluruhan, level awal yang paling banyak ditempati mentee adalah **{$mostCommonLevel}**, menandakan ini sebagai titik awal mayoritas peserta.";

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
                    $levelDistributionInterpretation[] = $interpretationText . implode(', ', $parts) . ".";
                }
            }

            $attendanceStatsInterpretation = [];
            if(!empty($attendanceStats)) {
                // We need to add faculty_name to the array to make it sortable
                $tempAttendanceStats = collect($attendanceStats)->map(function($stats, $facultyName) {
                    $stats['faculty_name'] = $facultyName;
                    return $stats;
                });

                $highestPlacement = $tempAttendanceStats->sortByDesc('placement_percentage')->first();
                $highestFinal = $tempAttendanceStats->sortByDesc('final_exam_percentage')->first();
                if($highestPlacement && $highestFinal) {
                    $attendanceStatsInterpretation[] = "Partisipasi tertinggi pada Placement Test dicatatkan oleh **{$highestPlacement['faculty_name']}** (" . number_format($highestPlacement['placement_percentage'], 1) . "%), sedangkan untuk Ujian Akhir, partisipasi tertinggi dari **{$highestFinal['faculty_name']}** (" . number_format($highestFinal['final_exam_percentage'], 1) . "%).";
                }
                
                $totalMentees = $tempAttendanceStats->sum('total_mentees');
                $totalPlacementAttended = $tempAttendanceStats->sum('placement_attended');
                $avgPlacementPercentage = $totalMentees > 0 ? ($totalPlacementAttended / $totalMentees) * 100 : 0;
                $attendanceStatsInterpretation[] = "Rata-rata tingkat kehadiran mentee untuk Placement Test di semua fakultas adalah **" . number_format($avgPlacementPercentage, 1) . "%**.";
            }

            // 14. Generate interpretations for 'Analisis Perbandingan' tab
            $scoreComparisonInterpretation = [];
            if (!empty($scoreComparisonData)) {
                $allProgramsData = collect($scoreComparisonData);
                $avgPlacement = $allProgramsData->avg('placement');
                $avgFinal = $allProgramsData->avg('final_exam');

                if ($avgFinal > $avgPlacement) {
                    $scoreComparisonInterpretation[] = "Secara umum, terdapat peningkatan performa mentee dengan rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih tinggi dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**).";
                } else {
                    $scoreComparisonInterpretation[] = "Perlu diperhatikan, rata-rata nilai Ujian Akhir (**" . number_format($avgFinal, 1) . "**) lebih rendah dari rata-rata nilai Placement Test (**" . number_format($avgPlacement, 1) . "**) secara keseluruhan.";
                }

                $bestImprovement = $allProgramsData->map(function ($scores, $program) {
                    return ['program' => $program, 'improvement' => $scores['final_exam'] - $scores['placement']];
                })->sortByDesc('improvement')->first();

                if ($bestImprovement && $bestImprovement['improvement'] > 0) {
                    $scoreComparisonInterpretation[] = "Peningkatan skor rata-rata terbesar terjadi pada program studi **{$bestImprovement['program']}** dengan kenaikan sebesar **" . number_format($bestImprovement['improvement'], 1) . " poin**.";
                }
            }

            $scoreProgressionInterpretation = [];
            if(!empty($scoreProgressionAnalysis)) {
                $totalUp = collect($scoreProgressionAnalysis)->sum('up');
                $totalDown = collect($scoreProgressionAnalysis)->sum('down');
                $totalSame = collect($scoreProgressionAnalysis)->sum('same');
                $totalMentees = $totalUp + $totalDown + $totalSame;

                if ($totalMentees > 0) {
                    $scoreProgressionInterpretation[] = "Dari total **" . number_format($totalMentees) . " mentee**, sebanyak **" . number_format(($totalUp / $totalMentees) * 100, 1) . "%** mengalami kenaikan nilai, sementara **" . number_format(($totalDown / $totalMentees) * 100, 1) . "%** mengalami penurunan.";
                }

                $facultyMostImproved = collect($scoreProgressionAnalysis)->map(function ($stats, $faculty) {
                    $total = $stats['up'] + $stats['down'] + $stats['same'];
                    return ['faculty' => $faculty, 'percentage_up' => $total > 0 ? ($stats['up'] / $total) * 100 : 0];
                })->sortByDesc('percentage_up')->first();

                if ($facultyMostImproved) {
                    $scoreProgressionInterpretation[] = "Fakultas **{$facultyMostImproved['faculty']}** menunjukkan persentase mentee yang nilainya naik paling tinggi, yaitu **" . number_format($facultyMostImproved['percentage_up'], 1) . "%** dari total mentee di fakultas tersebut.";
                }
            }

            $levelProgressionInterpretation = [];
            if(!empty($levelProgressionByFacultyAndLevel)) {
                $totalPromoted = 0;
                $totalDemoted = 0;
                $totalStayed = 0;

                foreach($levelProgressionByFacultyAndLevel as $faculty => $levels) {
                    foreach($levels as $level => $stats) {
                        $totalPromoted += $stats['up'];
                        $totalDemoted += $stats['down'];
                        $totalStayed += $stats['same'];
                    }
                }
                $totalTransitions = $totalPromoted + $totalDemoted + $totalStayed;

                if($totalTransitions > 0) {
                    $levelProgressionInterpretation[] = "Secara keseluruhan, **" . number_format(($totalPromoted / $totalTransitions) * 100, 1) . "%** mentee berhasil naik level, sementara **" . number_format(($totalDemoted / $totalTransitions) * 100, 1) . "%** mengalami penurunan level.";
                }
            }

            return array_merge(
                compact(
                    'facultyStats', 
                    'programStats', 
                    'levelDistribution', 
                    'levels', 
                    'scoreComparisonData', 
                    'levelProgressionByFacultyAndLevel', 
                    'scoreProgressionAnalysis', 
                    'attendanceStats',
                    'individualAnalyses',
                    'mostActiveMentors',
                    'mentorsNeedingAttention',
                    'progressiveGroups',
                    'stagnantGroups',
                    'facultyStatsInterpretation',
                    'levelDistributionInterpretation',
                    'attendanceStatsInterpretation',
                    'scoreComparisonInterpretation',
                    'scoreProgressionInterpretation',
                    'levelProgressionInterpretation',
                    'levelProgressionByFacultyInterpretation',
                    'levelNames'
                ),
                $levelEffectivenessData
            );
        });

        return view('admin.statistics.index', $data);
    }

    /**
     * Get data for level effectiveness analysis.
     *
     * @param array $levels
     * @return array
     */
    private function getLevelEffectivenessData(array $levels): array
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