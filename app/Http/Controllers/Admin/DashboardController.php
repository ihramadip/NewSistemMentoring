<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\MentorApplication;
use App\Models\MentoringGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Chart Data: Mentees per Faculty
        $menteesByFaculty = User::where('role_id', 3) // Mentee role
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name', DB::raw('count(*) as total'))
            ->groupBy('faculties.name')
            ->pluck('total', 'name');

        $facultyLabels = $menteesByFaculty->keys();
        $facultyData = $menteesByFaculty->values();

        // Chart Data: Mentor Applications in the last 7 days
        $applicationsLast7Days = MentorApplication::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total', 'date');

        // Fill in missing dates with 0 counts
        $applicationDates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $applicationDates[$date] = $applicationsLast7Days->get($date, 0);
        }

        $applicationLabels = $applicationDates->keys()->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('d M');
        });
        $applicationData = $applicationDates->values();


        // Widget Data
        $newApplications = MentorApplication::where('status', 'pending')
            ->whereHas('user')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $latestAnnouncements = Announcement::where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'mentorCount',
            'menteeCount',
            'groupCount',
            'pendingApplicationsCount',
            'newApplications',
            'latestAnnouncements',
            'facultyLabels',
            'facultyData',
            'applicationLabels',
            'applicationData'
        ));
    }
}
