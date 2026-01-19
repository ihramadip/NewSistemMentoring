<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementTest;
use App\Models\MentoringGroup;
use App\Models\Attendance;
use App\Models\ProgressReport;
use App\Models\Session;

/**
 * MenteeReportController
 *
 * Controller untuk menampilkan laporan perkembangan mentoring kepada mentee (MODULE C #1: Mentoring Wajib)
 * Mentee dapat melihat laporan perkembangan mereka: hasil placement test, kehadiran, nilai, dll
 *
 * Fitur:
 * - Index: menampilkan laporan perkembangan mentoring secara keseluruhan
 *
 * Data structure:
 * - PlacementTest: hasil tes penempatan mentee
 * - MentoringGroup: informasi kelompok mentoring mentee
 * - Session: daftar sesi mentoring
 * - Attendance: informasi kehadiran mentee di setiap sesi
 * - ProgressReport: laporan perkembangan dan nilai mentee per sesi
 *
 * Authorization:
 * - Hanya mentee yang bisa melihat laporan mereka sendiri
 * - Admin juga bisa mengakses (untuk monitoring)
 *
 * Flow:
 * 1. Mentee akses halaman laporan
 * 2. Controller kumpulkan semua data perkembangan
 * 3. Hitung statistik kehadiran dan nilai
 * 4. Tampilkan laporan di view
 *
 * @package App\Http\Controllers
 */
class MenteeReportController extends Controller
{
    /**
     * Menampilkan laporan perkembangan mentoring untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Ambil hasil placement test mentee (dengan finalLevel)
     * 3. Ambil mentoring group mentee (dengan mentor & level)
     * 4. Jika mentee memiliki group:
     *    - Ambil semua sesi untuk group tersebut
     *    - Eager load attendance & progress report milik mentee ini
     *    - Urutkan sesi berdasarkan tanggal (asc)
     * 5. Hitung statistik:
     *    - Total sessions
     *    - Jumlah sesi yang dihadiri
     *    - Rata-rata skor dari progress reports
     * 6. Return view dengan semua data laporan
     *
     * Data gathering:
     * - PlacementTest: where('mentee_id', $user->id) with finalLevel
     * - MentoringGroup: user->mentoringGroupsAsMentee() with mentor & level
     * - Sessions: where('mentoring_group_id', $mentoringGroup->id) with attendances & progressReports
     *
     * Statistics calculation:
     * - Total sessions: count all sessions in group
     * - Attended sessions: filter sessions where attendance status = 'hadir'
     * - Average score: avg of all progress report scores for mentee
     *
     * @return \Illuminate\View\View View laporan perkembangan mentee dengan semua data
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil hasil placement test mentee (dengan finalLevel)
        $placementTest = PlacementTest::where('mentee_id', $user->id)
                                    ->with('finalLevel')  // Eager load level hasil placement
                                    ->first();

        // Ambil mentoring group mentee (dengan mentor & level)
        $mentoringGroup = $user->mentoringGroupsAsMentee()
                              ->with(['mentor', 'level'])  // Eager load mentor & level info
                              ->first();

        // Ambil semua sesi untuk group mentee, dengan attendance & progress report milik mentee ini
        $sessionsData = collect();
        if ($mentoringGroup) {
            $sessionsData = Session::where('mentoring_group_id', $mentoringGroup->id)
                                ->with(['attendances' => function($query) use ($user) {
                                    $query->where('mentee_id', $user->id);  // Hanya attendance milik mentee ini
                                }, 'progressReports' => function($query) use ($user) {
                                    $query->where('mentee_id', $user->id);  // Hanya progress report milik mentee ini
                                }])
                                ->orderBy('date', 'asc')  // Urutkan sesi berdasarkan tanggal
                                ->get();
        }

        // Hitung statistik kehadiran dan nilai
        $totalSessions = $sessionsData->count();

        // Hitung jumlah sesi yang dihadiri (status = 'hadir')
        $attendedSessions = $sessionsData->filter(function($session) use ($user) {
            $attendance = $session->attendances->where('mentee_id', $user->id)->first();
            return $attendance && $attendance->status === 'hadir';
        })->count();

        // Hitung rata-rata skor dari progress reports
        $allProgressReports = $sessionsData->flatMap(function($session) use ($user) {
            return $session->progressReports->where('mentee_id', $user->id);
        });

        $averageScore = $allProgressReports->isNotEmpty() ? $allProgressReports->avg('score') : 0;

        // Return view laporan dengan semua data
        return view('mentee.report.index', compact(
            'user',
            'placementTest',
            'mentoringGroup',
            'sessionsData',
            'totalSessions',
            'attendedSessions',
            'averageScore'
        ));
    }
}
