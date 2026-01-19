<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\MentorApplication;
use App\Models\MentoringGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Controller untuk admin dashboard (MODULE D: Admin Dashboard & Reports)
 * Display overview data system dengan stat cards, charts, & widgets
 *
 * Fitur:
 * - Stat Cards: total mentors, mentees, groups, pending applications
 * - Chart #1: Mentees by Faculty (bar/pie chart)
 * - Chart #2: Mentor Applications last 7 days (line chart)
 * - Widget #1: New Pending Applications (latest 5)
 * - Widget #2: Latest Announcements (latest 5 published)
 * - All data eager loaded to prevent N+1 query problems
 *
 * Data aggregation:
 * - Stat: simple count queries per role_id
 * - Chart mentees by faculty: join users & faculties, groupBy name, raw count
 * - Chart applications: select date & count, fill missing dates with 0
 * - Widgets: eager load relationships, order by latest, take 5
 *
 * @package App\Http\Controllers\Admin
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan admin dashboard
     *
     * Proses:
     * 1. Count stat cards: mentors (role_id=2), mentees (role_id=3), groups, pending applications
     * 2. Build Chart #1 (Mentees by Faculty):
     *    - Join users & faculties table
     *    - Filter role_id = 3 (mentee)
     *    - Group by faculty name
     *    - Count total per faculty
     * 3. Build Chart #2 (Applications last 7 days):
     *    - Select DATE(created_at) & count
     *    - Filter last 7 days
     *    - Group by date
     *    - Fill missing dates dengan 0 count
     *    - Format date ke "dd M" format (e.g., "17 Jan")
     * 4. Build Widget #1 (New pending applications):
     *    - Query pending status, eager load user, latest created_at
     *    - Take 5 records untuk display
     * 5. Build Widget #2 (Latest announcements):
     *    - Query published only (is_published = true)
     *    - Order by published_at latest
     *    - Take 5 records untuk display
     * 6. Return view dengan semua data di-compact
     *
     * Optimization:
     * - Eager loading: newApplications.user, latestAnnouncements.author
     * - Group aggregation: chartData via DB::raw untuk raw SQL count
     * - Missing dates: fill dengan 0 untuk continuous chart data
     *
     * @return \Illuminate\View\View View admin dashboard dengan all stats & charts & widgets
     */
    public function index()
    {
        // ========== STAT CARDS ==========
        // Count mentors: role_id = 2
        $mentorCount = User::where('role_id', 2)->count();

        // Count mentees: role_id = 3
        $menteeCount = User::where('role_id', 3)->count();

        // Count total mentoring groups
        $groupCount = MentoringGroup::count();

        // Count pending mentor applications (status = 'pending')
        $pendingApplicationsCount = MentorApplication::where('status', 'pending')->count();

        // ========== CHART #1: MENTEES BY FACULTY ==========
        // Query: join users & faculties, filter mentee, count per faculty
        $menteesByFaculty = User::where('role_id', 3) // Filter role_id = 3 (Mentee)
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name', DB::raw('count(*) as total')) // Select faculty name & count
            ->groupBy('faculties.name') // Group by faculty name
            ->pluck('total', 'name'); // Pluck sebagai key-value (faculty_name => total)

        // Extract keys (faculty names) & values (counts) untuk chart library
        $facultyLabels = $menteesByFaculty->keys();
        $facultyData = $menteesByFaculty->values();

        // ========== CHART #2: MENTOR APPLICATIONS LAST 7 DAYS ==========
        // Query: select date & count, last 7 days, order by date ascending
        $applicationsLast7Days = MentorApplication::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7)) // Filter last 7 days
            ->groupBy('date') // Group by date
            ->orderBy('date', 'asc') // Order ascending untuk chronological
            ->pluck('total', 'date'); // Pluck as key-value (date => total)

        // Fill missing dates dengan 0 count (untuk continuous line di chart)
        $applicationDates = collect();
        for ($i = 6; $i >= 0; $i--) {
            // Loop 6 hari sebelumnya sampai hari ini (7 hari total)
            $date = now()->subDays($i)->format('Y-m-d');
            // Get count dari query, default 0 jika tanggal tidak ada
            $applicationDates[$date] = $applicationsLast7Days->get($date, 0);
        }

        // Format dates ke "dd M" format (e.g., "17 Jan") untuk chart display
        $applicationLabels = $applicationDates->keys()->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('d M');
        });
        // Extract values (counts) untuk chart data
        $applicationData = $applicationDates->values();

        // ========== WIDGET #1: NEW PENDING APPLICATIONS ==========
        // Query: pending status, eager load user, latest created_at, take 5
        $newApplications = MentorApplication::where('status', 'pending') // Filter status = 'pending'
            ->whereHas('user') // Ensure user relationship exists
            ->with('user') // Eager load user data (prevent N+1)
            ->latest() // Order by created_at desc
            ->take(5) // Limit to 5 records
            ->get();

        // ========== WIDGET #2: LATEST ANNOUNCEMENTS ==========
        // Query: published only, latest published_at, take 5
        $latestAnnouncements = Announcement::where('is_published', true) // Filter published only
            ->latest('published_at') // Order by published_at desc
            ->take(5) // Limit to 5 records
            ->get();

        // ========== RETURN VIEW ==========
        // Compact all data untuk view render
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
