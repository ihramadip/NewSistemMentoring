<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $mentor = Auth::user();
        $groupIds = $mentor->mentoringGroupsAsMentor()->pluck('id');

        // Stat Card Data
        $groupCount = $groupIds->count();
        $menteeCount = User::whereHas('mentoringGroupsAsMentee', fn($q) => $q->whereIn('mentoring_group_id', $groupIds))->count();

        $sessionsInThePast = Session::whereIn('mentoring_group_id', $groupIds)
            ->where('date', '<', now())
            ->withCount('progressReports')
            ->get();

        $pendingReportsCount = $sessionsInThePast->filter(function ($session) use ($menteeCount) {
            // A session needs a report if the number of reports is less than the number of mentees.
            // This is a simplified logic. A more precise logic would check per-mentee.
            return $session->progress_reports_count < $menteeCount;
        })->count();


        // Widget Data
        $upcomingSessions = Session::whereIn('mentoring_group_id', $groupIds)
            ->where('date', '>=', now())
            ->with('mentoringGroup')
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();
            
        $groups = $mentor->mentoringGroupsAsMentor()->with('level')->get();

        return view('mentor.dashboard', compact(
            'groupCount',
            'menteeCount',
            'pendingReportsCount',
            'upcomingSessions',
            'groups'
        ));
    }
}
