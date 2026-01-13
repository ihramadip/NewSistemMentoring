<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentor = Auth::user();
        $groupIds = $mentor->mentoringGroupsAsMentor()->pluck('id');

        // Eager load relationships for efficiency
        $mentees = User::whereHas('mentoringGroupsAsMentee', function ($query) use ($groupIds) {
                $query->whereIn('mentoring_group_id', $groupIds);
            })
            ->with(['progressReports', 'attendances', 'mentoringGroupsAsMentee'])
            ->get();
            
        // Get total session counts for each group
        $sessionCounts = Session::whereIn('mentoring_group_id', $groupIds)
            ->select('mentoring_group_id', DB::raw('count(*) as total'))
            ->groupBy('mentoring_group_id')
            ->pluck('total', 'mentoring_group_id');

        // Calculate stats for each mentee
        $mentees->each(function ($mentee) use ($sessionCounts) {
            // Calculate average score
            $scores = $mentee->progressReports->pluck('score')->filter();
            $mentee->average_score = $scores->isNotEmpty() ? round($scores->avg()) : 'N/A';

            // Calculate attendance rate
            $presentCount = $mentee->attendances->where('status', 'hadir')->count();
            
            // Find the group this mentee belongs to among the mentor's groups
            $menteeGroup = $mentee->mentoringGroupsAsMentee->first();
            $totalSessions = $menteeGroup ? ($sessionCounts[$menteeGroup->id] ?? 0) : 0;
            
            $mentee->attendance_rate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;
            $mentee->attendance_summary = "{$presentCount}/{$totalSessions}";
        });

        return view('mentor.reports.index', compact('mentees'));
    }
}
