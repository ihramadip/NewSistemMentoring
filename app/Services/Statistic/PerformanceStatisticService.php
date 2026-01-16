<?php

namespace App\Services\Statistic;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PerformanceStatisticService
{
    /**
     * Get mentor activity and group performance analysis.
     *
     * @return array
     */
    public function getAnalysis(): array
    {
        $mentorActivity = $this->getMentorActivity();
        $groupPerformance = $this->getGroupPerformance();

        return array_merge($mentorActivity, $groupPerformance);
    }

    /**
     * Analyzes mentor activity.
     *
     * @return array
     */
    private function getMentorActivity(): array
    {
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

        return compact('mostActiveMentors', 'mentorsNeedingAttention');
    }

    /**
     * Analyzes group performance based on score progression.
     *
     * @return array
     */
    private function getGroupPerformance(): array
    {
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

            $group->avg_score_increase_points = $pointIncrease;
            $group->avg_score_increase_percentage = $placementScore > 0 ? ($pointIncrease / $placementScore) * 100 : 0;

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

        return compact('progressiveGroups', 'stagnantGroups');
    }
}
