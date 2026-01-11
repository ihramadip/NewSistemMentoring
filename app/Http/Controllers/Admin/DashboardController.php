<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use App\Models\MentoringGroup;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat Card Data
        // Assuming role_id 2 is Mentor and 3 is Mentee.
        $mentorCount = User::where('role_id', 2)->count();
        $menteeCount = User::where('role_id', 3)->count();
        $groupCount = MentoringGroup::count();
        $pendingApplicationsCount = MentorApplication::where('status', 'pending')->count();

        // Widget Data
        $newApplications = MentorApplication::where('status', 'pending')
            ->whereHas('user')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'mentorCount',
            'menteeCount',
            'groupCount',
            'pendingApplicationsCount',
            'newApplications'
        ));
    }
}
