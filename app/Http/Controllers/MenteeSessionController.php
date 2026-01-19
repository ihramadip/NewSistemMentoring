<?php

namespace App\Http\Controllers;

use App\Models\MentoringGroup;
use App\Services\MenteeSessionService;
use Illuminate\Support\Facades\Auth;

/**
 * MenteeSessionController
 *
 * Controller untuk menampilkan sesi mentoring kepada mentee (MODULE C #1: Mentoring Wajib & #2: Mentoring Tambahan)
 * Mentee dapat melihat daftar sesi wajib dan sesi tambahan dalam kelompok mereka
 *
 * Fitur:
 * - Index: menampilkan daftar sesi mentoring (wajib dan tambahan) untuk mentee
 *
 * Data structure:
 * - Session: informasi sesi mentoring (tanggal, judul, deskripsi, dll)
 * - AdditionalSession: informasi sesi tambahan (topik, tanggal, status, bukti, dll)
 * - MentoringGroup: informasi kelompok mentoring mentee
 *
 * Authorization:
 * - Hanya mentee yang bisa melihat sesi dalam kelompok mereka
 * - Admin juga bisa mengakses (untuk monitoring), tapi tidak memiliki kelompok
 *
 * Flow:
 * 1. Mentee akses halaman sesi
 * 2. Controller panggil service untuk ambil data sesi
 * 3. Tampilkan sesi wajib dan tambahan di view
 *
 * @package App\Http\Controllers
 */
class MenteeSessionController extends Controller
{
    protected $menteeSessionService;

    /**
     * Constructor untuk inject MenteeSessionService
     *
     * @param MenteeSessionService $menteeSessionService Service untuk mengelola data sesi mentee
     */
    public function __construct(MenteeSessionService $menteeSessionService)
    {
        $this->menteeSessionService = $menteeSessionService;
    }

    /**
     * Menampilkan daftar sesi mentoring untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Jika user adalah admin:
     *    - Tampilkan view dengan empty collections dan null group
     *    - Admin tidak memiliki group, ini normal untuk monitoring
     * 3. Jika user adalah mentee:
     *    - Panggil MenteeSessionService->getSessions() untuk ambil data sesi
     *    - Service mengembalikan array dengan sessions, additionalSessions, dan mentoringGroup
     *    - Jika mentee tidak memiliki mentoring group, redirect ke dashboard dengan warning
     * 4. Return view dengan data sesi
     *
     * Service integration:
     * - Gunakan MenteeSessionService untuk mengelola business logic pengambilan data sesi
     * - Service handle pengambilan mandatory sessions dan additional sessions
     *
     * Authorization handling:
     * - Cek role user untuk menentukan response jika tidak ada group
     * - Admin tidak memiliki group, tampilkan empty state
     * - Mentee harus memiliki group untuk akses fitur ini
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirect ke dashboard jika tidak ada group atau view daftar sesi
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Jika user adalah admin, tampilkan view dengan empty state
        // Admin tidak memiliki group, ini normal untuk monitoring
        if ($user->role->name === 'Admin') {
            return view('mentee.sessions.index', [
                'sessions' => collect(),         // Empty collection untuk sesi wajib
                'additionalSessions' => collect(), // Empty collection untuk sesi tambahan
                'mentoringGroup' => null         // Null untuk group karena admin tidak punya
            ]);
        }

        // Panggil service untuk ambil data sesi untuk mentee
        $sessionData = $this->menteeSessionService->getSessions($user);

        // Jika mentee tidak memiliki mentoring group, redirect ke dashboard
        if (!$sessionData['mentoringGroup']) {
            return redirect()->route('dashboard')
                           ->with('warning', 'You have not been assigned to a mentoring group yet to view sessions.');
        }

        // Return view dengan data sesi dari service
        return view('mentee.sessions.index', $sessionData);
    }
}
