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

/**
 * MenteeDashboardController
 *
 * Controller untuk dashboard utama mentee (MODULE C #1: Mentoring Wajib & #5: Diskusi Terbuka)
 * Menyediakan ringkasan informasi penting untuk mentee: placement test, group, pengumuman, sesi, dll
 *
 * Fitur:
 * - Index: halaman utama dashboard dengan berbagai ringkasan data
 *
 * Data structure:
 * - User: informasi dasar mentee yang sedang login
 * - PlacementTest: hasil tes penempatan mentee
 * - MentoringGroup: informasi kelompok mentoring mentee
 * - Announcement: pengumuman terbaru untuk mentee
 * - Session: sesi mentoring mendatang
 * - Attendance & ProgressReport: statistik kehadiran & nilai
 *
 * Summary components:
 * - Placement Test Summary: hasil dan level dari placement test
 * - Mentoring Group Summary: informasi mentor dan level kelompok
 * - Latest Announcements: 3 pengumuman terbaru yang telah dipublish
 * - Upcoming Sessions: 3 sesi mendatang dalam kelompok
 * - Attendance & Score Summary: statistik kehadiran dan rata-rata nilai
 *
 * Flow:
 * 1. Mentee login dan akses dashboard
 * 2. Controller kumpulkan semua data summary
 * 3. Hitung statistik kehadiran dan nilai
 * 4. Tampilkan semua informasi di dashboard
 *
 * @package App\Http\Controllers
 */
class MenteeDashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login (mentee)
     * 2. Ambil placement test summary (hasil & level)
     * 3. Ambil mentoring group summary (mentor & level)
     * 4. Ambil latest announcements (top 3 recent, sudah dipublish)
     * 5. Jika ada mentoring group:
     *    - Ambil upcoming sessions (next 3, berdasarkan tanggal)
     *    - Hitung total sessions, attended sessions, average score
     * 6. Return view dengan semua data summary
     *
     * Data gathering:
     * - PlacementTest: where('mentee_id', $user->id) with finalLevel
     * - MentoringGroup: user->mentoringGroupsAsMentee() with mentor & level
     * - Announcement: whereNotNull('published_at') and published_at <= now(), take(3)
     * - Session: where('mentoring_group_id', $mentoringGroup->id) with upcoming dates
     *
     * Statistics calculation:
     * - Total sessions: count all sessions in group
     * - Attended sessions: filter sessions where attendance status = 'hadir'
     * - Average score: avg of all progress report scores for mentee
     *
     * @return \Illuminate\View\View View dashboard mentee dengan semua summary data
     */
    public function index()
    {
        // Ambil user yang sedang login (mentee)
        $user = Auth::user();

        // Placement Test Summary: hasil tes penempatan mentee
        $placementTest = PlacementTest::where('mentee_id', $user->id)
                                    ->with('finalLevel')  // Eager load level hasil placement
                                    ->first();

        // Mentoring Group Summary: informasi kelompok mentoring mentee
        $mentoringGroup = $user->mentoringGroupsAsMentee()
                              ->with(['mentor', 'level'])  // Eager load mentor & level info
                              ->first();

        // Latest Announcements: 3 pengumuman terbaru yang telah dipublish
        $latestAnnouncements = Announcement::whereNotNull('published_at')
                                          ->where('published_at', '<=', Carbon::now())
                                          ->where(function($query) {
                                              $query->where('target_role', 'All')
                                                    ->orWhere('target_role', 'Mentee');
                                          })
                                          ->orderBy('published_at', 'desc')
                                          ->take(3)
                                          ->get();

        // Inisialisasi variabel untuk upcoming sessions dan statistik
        $upcomingSessions = collect();
        $totalSessions = 0;
        $attendedSessions = 0;
        $averageScore = 'N/A';

        // Jika mentee tergabung dalam mentoring group
        if ($mentoringGroup) {
            // Upcoming Sessions: 3 sesi mendatang dalam kelompok
            $upcomingSessions = Session::where('mentoring_group_id', $mentoringGroup->id)
                                      ->where('date', '>=', Carbon::now())  // Sesi yang belum lewat
                                      ->orderBy('date', 'asc')             // Urutkan dari terdekat
                                      ->take(3)                           // Ambil 3 terdekat
                                      ->get();

            // Ambil semua sesi dalam kelompok untuk perhitungan statistik
            $allSessionsForGroup = Session::where('mentoring_group_id', $mentoringGroup->id)
                                        ->with(['attendances' => function($query) use ($user) {
                                            $query->where('mentee_id', $user->id);  // Hanya attendance milik mentee ini
                                        }, 'progressReports' => function($query) use ($user) {
                                            $query->where('mentee_id', $user->id);  // Hanya progress report milik mentee ini
                                        }])
                                        ->get();

            // Hitung total sessions dalam kelompok
            $totalSessions = $allSessionsForGroup->count();

            // Hitung jumlah sesi yang dihadiri (status = 'hadir')
            $attendedSessions = $allSessionsForGroup->filter(function($session) use ($user) {
                $attendance = $session->attendances->where('mentee_id', $user->id)->first();
                return $attendance && $attendance->status === 'hadir';
            })->count();

            // Hitung rata-rata skor dari progress reports
            $allProgressReports = $allSessionsForGroup->flatMap(function($session) use ($user) {
                return $session->progressReports->where('mentee_id', $user->id);
            });

            if ($allProgressReports->isNotEmpty()) {
                $averageScore = $allProgressReports->avg('score');
                $averageScore = is_numeric($averageScore) ? number_format($averageScore, 2) : 'N/A';
            }
        }

        // Return view dashboard dengan semua data summary
        return view('dashboard', compact(
            'user',
            'placementTest',
            'mentoringGroup',
            'latestAnnouncements',
            'upcomingSessions',
            'totalSessions',
            'attendedSessions',
            'averageScore'
        ));
    }
}
