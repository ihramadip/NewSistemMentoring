<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementTest;
use App\Models\MentoringGroup;
use App\Models\Announcement;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\ProgressReport;
use Carbon\Carbon;

class MenteeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Placement Test Summary
        $placementTest = PlacementTest::where('mentee_id', $user->id)
                                    ->with('finalLevel')
                                    ->first();
        
        // Mentoring Group Summary
        $mentoringGroup = $user->mentoringGroupsAsMentee()->with(['mentor', 'level'])->first();

        // Latest Announcements (e.g., top 3 recent)
        $latestAnnouncements = Announcement::whereNotNull('published_at')
                                            ->where('published_at', '<=', Carbon::now())
                                            ->orderBy('published_at', 'desc')
                                            ->take(3)
                                            ->get();

        // Upcoming Sessions (e.g., next 3 upcoming for their group)
        $upcomingSessions = collect();
        $totalSessions = 0;
        $attendedSessions = 0;
        $averageScore = 'N/A';

        if ($mentoringGroup) {
            $upcomingSessions = Session::where('mentoring_group_id', $mentoringGroup->id)
                                        ->where('date', '>=', Carbon::now())
                                        ->orderBy('date', 'asc')
                                        ->take(3)
                                        ->get();

            // For attendance and progress summary
            $allSessionsForGroup = Session::where('mentoring_group_id', $mentoringGroup->id)
                                        ->with(['attendances' => function($query) use ($user) {
                                            $query->where('mentee_id', $user->id);
                                        }, 'progressReports' => function($query) use ($user) {
                                            $query->where('mentee_id', $user->id);
                                        }])
                                        ->get();
            
            $totalSessions = $allSessionsForGroup->count();
            $attendedSessions = $allSessionsForGroup->filter(function($session) use ($user) {
                return $session->attendances->where('mentee_id', $user->id)->first()->status === 'hadir' ?? false;
            })->count();
            $averageScore = $allSessionsForGroup->flatMap(function($session) use ($user) {
                return $session->progressReports->where('mentee_id', $user->id);
            })->avg('score');
            $averageScore = is_numeric($averageScore) ? number_format($averageScore, 2) : 'N/A';
        }

        return view('dashboard', compact('user', 'placementTest', 'mentoringGroup', 'latestAnnouncements', 'upcomingSessions', 'totalSessions', 'attendedSessions', 'averageScore'));
    }
}
