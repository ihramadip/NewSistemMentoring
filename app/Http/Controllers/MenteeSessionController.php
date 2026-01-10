<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MentoringGroup;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\ProgressReport;

class MenteeSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure the user is a mentee
        if ($user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Only mentees can view sessions.');
        }

        // Get the mentoring group(s) the mentee is part of
        $mentoringGroup = $user->mentoringGroupsAsMentee()->first();

        if (!$mentoringGroup) {
            return redirect()->route('dashboard')->with('warning', 'You have not been assigned to a mentoring group yet to view sessions.');
        }

        // Get all sessions for this mentee's group, with their attendance and progress report for each
        $sessions = Session::where('mentoring_group_id', $mentoringGroup->id)
                            ->with(['attendances' => function($query) use ($user) {
                                $query->where('mentee_id', $user->id);
                            }, 'progressReports' => function($query) use ($user) {
                                $query->where('mentee_id', $user->id);
                            }])
                            ->orderBy('date', 'asc')
                            ->get();

        return view('mentee.sessions.index', compact('sessions', 'mentoringGroup'));
    }
}
