<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AttendanceStatisticService
{
    /**
     * Get attendance statistics per faculty.
     *
     * @param Collection $allFaculties
     * @return array
     */
    public function getAnalysis(Collection $allFaculties): array
    {
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
        foreach ($allFaculties as $faculty) { // Iterate over all faculties to ensure all are included
            $total = $totalMenteesByFaculty[$faculty] ?? 0;
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

        $interpretation = [];
        if(!empty($attendanceStats)) {
            $tempAttendanceStats = collect($attendanceStats)->map(function($stats, $facultyName) {
                $stats['faculty_name'] = $facultyName;
                return $stats;
            });

            $highestPlacement = $tempAttendanceStats->sortByDesc('placement_percentage')->first();
            $highestFinal = $tempAttendanceStats->sortByDesc('final_exam_percentage')->first();
            if($highestPlacement && $highestFinal) {
                $interpretation[] = "Partisipasi tertinggi pada Placement Test dicatatkan oleh **{$highestPlacement['faculty_name']}** (" . number_format($highestPlacement['placement_percentage'], 1) . "%), sedangkan untuk Ujian Akhir, partisipasi tertinggi dari **{$highestFinal['faculty_name']}** (" . number_format($highestFinal['final_exam_percentage'], 1) . "%).";
            }
            
            $totalMentees = $tempAttendanceStats->sum('total_mentees');
            $totalPlacementAttended = $tempAttendanceStats->sum('placement_attended');
            $avgPlacementPercentage = $totalMentees > 0 ? ($totalPlacementAttended / $totalMentees) * 100 : 0;
            $interpretation[] = "Rata-rata tingkat kehadiran mentee untuk Placement Test di semua fakultas adalah **" . number_format($avgPlacementPercentage, 1) . "%**.";
        }

        return [
            'attendanceStats' => $attendanceStats,
            'attendanceStatsInterpretation' => $interpretation,
        ];
    }
}
