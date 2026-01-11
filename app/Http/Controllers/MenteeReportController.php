<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementTest;
use App\Models\MentoringGroup;
use App\Models\Attendance;
use App\Models\ProgressReport;
use App\Models\Session;

class MenteeReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch Mentee's Placement Test results
        $placementTest = PlacementTest::where('mentee_id', $user->id)
                                    ->with('finalLevel')
                                    ->first();
        
        // Fetch Mentee's Mentoring Group
        $mentoringGroup = $user->mentoringGroupsAsMentee()->with(['mentor', 'level'])->first();

        // Fetch all sessions for this mentee's group, with their attendance and progress report for each
        $sessionsData = collect();
        if ($mentoringGroup) {
            $sessionsData = Session::where('mentoring_group_id', $mentoringGroup->id)
                                ->with(['attendances' => function($query) use ($user) {
                                    $query->where('mentee_id', $user->id);
                                }, 'progressReports' => function($query) use ($user) {
                                    $query->where('mentee_id', $user->id);
                                }])
                                ->orderBy('date', 'asc')
                                ->get();
        }

        // Calculate overall scores/summaries if needed (can be expanded later)
        $totalSessions = $sessionsData->count();
        $attendedSessions = $sessionsData->filter(function($session) use ($user) {
            return $session->attendances->where('mentee_id', $user->id)->first()->status === 'hadir' ?? false;
        })->count();
        $averageScore = $sessionsData->flatMap(function($session) use ($user) {
            return $session->progressReports->where('mentee_id', $user->id);
        })->avg('score');


        return view('mentee.report.index', compact('user', 'placementTest', 'mentoringGroup', 'sessionsData', 'totalSessions', 'attendedSessions', 'averageScore'));
    }
}
