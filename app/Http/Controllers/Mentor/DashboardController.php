<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Controller untuk dashboard utama mentor (MODULE A: Mentor Management)
 * Menyediakan ringkasan informasi penting untuk mentor: jumlah kelompok, jumlah mentee, laporan tertunda, dll
 *
 * Fitur:
 * - Index: halaman utama dashboard dengan berbagai ringkasan data
 *
 * Data structure:
 * - Mentor: informasi dasar mentor yang sedang login
 * - MentoringGroup: informasi kelompok mentoring yang ditangani mentor
 * - User: informasi mentee dalam kelompok mentor
 * - Session: sesi mentoring dalam kelompok mentor
 * - ProgressReport: laporan perkembangan mentee
 *
 * Summary components:
 * - Stat Cards: jumlah kelompok, jumlah mentee, jumlah laporan tertunda
 * - Widget: sesi mendatang, daftar kelompok
 *
 * Flow:
 * 1. Mentor login dan akses dashboard
 * 2. Controller kumpulkan semua data summary
 * 3. Hitung statistik jumlah kelompok, mentee, dan laporan tertunda
 * 4. Tampilkan semua informasi di dashboard
 *
 * @package App\Http\Controllers\Mentor
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard mentor
     *
     * Proses:
     * 1. Ambil mentor yang sedang login
     * 2. Ambil ID kelompok mentoring yang ditangani mentor
     * 3. Hitung statistik:
     *    - Jumlah kelompok mentoring
     *    - Jumlah mentee dalam kelompok mentor
     *    - Jumlah laporan yang tertunda (belum semua mentee dilaporkan)
     * 4. Ambil data widget:
     *    - Sesi mendatang (5 sesi terdekat)
     *    - Daftar kelompok mentoring dengan level
     * 5. Return view dengan semua data summary
     *
     * Data gathering:
     * - Group IDs: mentor->mentoringGroupsAsMentor()->pluck('id')
     * - Mentee count: whereHas('mentoringGroupsAsMentee') dengan group IDs
     * - Past sessions: whereIn('mentoring_group_id', $groupIds) and date < now()
     * - Upcoming sessions: whereIn('mentoring_group_id', $groupIds) and date >= now()
     *
     * Statistics calculation:
     * - Group count: count of mentoring group IDs
     * - Mentee count: count of users in mentor's groups
     * - Pending reports: sessions where progress reports < mentee count
     *
     * @return \Illuminate\View\View View dashboard mentor dengan semua summary data
     */
    public function index()
    {
        // Ambil mentor yang sedang login
        $mentor = Auth::user();

        // Ambil ID kelompok mentoring yang ditangani mentor
        $groupIds = $mentor->mentoringGroupsAsMentor()->pluck('id');

        // Stat Card Data: hitung jumlah kelompok dan mentee
        $groupCount = $groupIds->count();
        $menteeCount = User::whereHas('mentoringGroupsAsMentee', fn($q) => $q->whereIn('mentoring_group_id', $groupIds))->count();

        // Ambil sesi-sesi yang sudah lewat dengan jumlah laporan perkembangan
        $sessionsInThePast = Session::whereIn('mentoring_group_id', $groupIds)
            ->where('date', '<', now())  // Sesi yang sudah lewat
            ->withCount('progressReports')  // Hitung jumlah laporan perkembangan per sesi
            ->get();

        // Hitung jumlah laporan yang tertunda (belum semua mentee dilaporkan)
        $pendingReportsCount = $sessionsInThePast->filter(function ($session) use ($menteeCount) {
            // Sesi membutuhkan laporan jika jumlah laporan < jumlah mentee
            // Ini adalah logika sederhana; logika lebih presisi akan memeriksa per-mentee
            return $session->progress_reports_count < $menteeCount;
        })->count();

        // Widget Data: ambil 5 sesi mendatang
        $upcomingSessions = Session::whereIn('mentoring_group_id', $groupIds)
            ->where('date', '>=', now())  // Sesi yang belum lewat
            ->with('mentoringGroup')  // Eager load informasi kelompok
            ->orderBy('date', 'asc')  // Urutkan dari terdekat
            ->take(5)  // Ambil 5 terdekat
            ->get();

        // Ambil semua kelompok mentoring dengan level
        $groups = $mentor->mentoringGroupsAsMentor()
                        ->with('level')  // Eager load informasi level
                        ->get();

        // Return view dashboard dengan semua data summary
        return view('mentor.dashboard', compact(
            'groupCount',
            'menteeCount',
            'pendingReportsCount',
            'upcomingSessions',
            'groups'
        ));
    }
}
