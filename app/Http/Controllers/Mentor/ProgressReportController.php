<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ProgressReportController
 *
 * Controller untuk mengelola laporan perkembangan mentee oleh mentor (MODULE C #1: Mentoring Wajib)
 * Mentor dapat melihat laporan perkembangan dan statistik kehadiran dari mentee dalam kelompok mereka
 *
 * Fitur:
 * - Index: menampilkan daftar mentee dengan statistik perkembangan dan kehadiran
 *
 * Data structure:
 * - User: informasi mentee
 * - ProgressReport: laporan perkembangan dan nilai mentee
 * - Attendance: informasi kehadiran mentee di sesi mentoring
 * - Session: informasi sesi mentoring dalam kelompok
 *
 * Statistics calculated:
 * - Average score: rata-rata skor dari progress reports mentee
 * - Attendance rate: persentase kehadiran mentee di sesi
 * - Attendance summary: ringkasan kehadiran (jumlah hadir / total sesi)
 *
 * Authorization:
 * - Hanya mentor yang bisa mengakses laporan mentee dalam kelompok mereka
 *
 * Flow:
 * 1. Mentor akses halaman laporan perkembangan
 * 2. Controller ambil semua mentee dalam kelompok mentor
 * 3. Hitung statistik perkembangan dan kehadiran untuk tiap mentee
 * 4. Tampilkan daftar mentee dengan statistik di view
 *
 * @package App\Http\Controllers\Mentor
 */
class ProgressReportController extends Controller
{
    /**
     * Menampilkan daftar mentee dengan statistik perkembangan dan kehadiran
     *
     * Proses:
     * 1. Ambil mentor yang sedang login
     * 2. Ambil ID kelompok mentoring yang ditangani mentor
     * 3. Query mentee dalam kelompok mentor dengan eager load:
     *    - progressReports: untuk menghitung average score
     *    - attendances: untuk menghitung attendance rate
     *    - mentoringGroupsAsMentee: untuk menghubungkan ke kelompok
     * 4. Ambil jumlah total sesi untuk tiap kelompok
     * 5. Untuk tiap mentee, hitung statistik:
     *    - Average score: rata-rata skor dari progress reports
     *    - Attendance rate: persentase kehadiran (hadir / total sesi)
     *    - Attendance summary: ringkasan kehadiran (hadir / total sesi)
     * 6. Return view dengan daftar mentee dan statistik mereka
     *
     * Data retrieval:
     * - Mentee: whereHas('mentoringGroupsAsMentee') dengan group IDs
     * - Eager load: ['progressReports', 'attendances', 'mentoringGroupsAsMentee']
     * - Session counts: pluck('total', 'mentoring_group_id') untuk lookup cepat
     *
     * Statistics calculation:
     * - Average score: avg() dari skor progress reports
     * - Attendance rate: (jumlah hadir / total sesi) * 100
     * - Attendance summary: "{hadir}/{total}" format
     *
     * @return \Illuminate\View\View View daftar mentee dengan statistik perkembangan
     */
    public function index()
    {
        // Ambil mentor yang sedang login
        $mentor = Auth::user();

        // Ambil ID kelompok mentoring yang ditangani mentor
        $groupIds = $mentor->mentoringGroupsAsMentor()->pluck('id');

        // Query mentee dalam kelompok mentor dengan eager load untuk efisiensi
        $mentees = User::whereHas('mentoringGroupsAsMentee', function ($query) use ($groupIds) {
                $query->whereIn('mentoring_group_id', $groupIds);
            })
            ->with(['progressReports', 'attendances', 'mentoringGroupsAsMentee'])
            ->get();

        // Ambil jumlah total sesi untuk tiap kelompok
        $sessionCounts = Session::whereIn('mentoring_group_id', $groupIds)
            ->select('mentoring_group_id', DB::raw('count(*) as total'))
            ->groupBy('mentoring_group_id')
            ->pluck('total', 'mentoring_group_id');

        // Hitung statistik untuk tiap mentee
        $mentees->each(function ($mentee) use ($sessionCounts) {
            // Hitung average score dari progress reports
            $scores = $mentee->progressReports->pluck('score')->filter();
            $mentee->average_score = $scores->isNotEmpty() ? round($scores->avg()) : 'N/A';

            // Hitung jumlah kehadiran (status = 'hadir')
            $presentCount = $mentee->attendances->where('status', 'hadir')->count();

            // Temukan kelompok yang diikuti mentee di antara kelompok mentor
            $menteeGroup = $mentee->mentoringGroupsAsMentee->first();
            $totalSessions = $menteeGroup ? ($sessionCounts[$menteeGroup->id] ?? 0) : 0;

            // Hitung persentase kehadiran dan ringkasan
            $mentee->attendance_rate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;
            $mentee->attendance_summary = "{$presentCount}/{$totalSessions}";
        });

        // Return view dengan daftar mentee dan statistik mereka
        return view('mentor.reports.index', compact('mentees'));
    }
}
