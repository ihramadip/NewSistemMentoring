<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\MentoringGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\ProgressReport;

/**
 * SessionController
 *
 * Controller untuk mengelola sesi mentoring oleh mentor (MODULE C #1: Mentoring Wajib)
 * Mentor dapat membuat, melihat, mengupdate, dan menghapus sesi mentoring
 * Juga dapat mengisi absensi dan laporan perkembangan mentee
 *
 * Fitur:
 * - Index: menampilkan daftar sesi mentoring milik mentor
 * - Show: menampilkan detail sesi dengan absensi dan laporan mentee
 * - SelectGroupForSession: menampilkan form untuk memilih kelompok sebelum membuat sesi
 * - Create: menampilkan form untuk membuat sesi baru untuk kelompok tertentu
 * - Store: menyimpan sesi baru ke database
 * - Update: memperbarui absensi dan laporan perkembangan mentee
 * - Destroy: menghapus sesi mentoring
 *
 * Data structure:
 * - Session: informasi sesi mentoring (tanggal, judul, deskripsi, nomor sesi)
 * - Attendance: informasi kehadiran mentee di sesi
 * - ProgressReport: laporan perkembangan dan nilai mentee di sesi
 * - MentoringGroup: informasi kelompok mentoring
 *
 * Authorization:
 * - Hanya mentor yang bisa mengakses sesi dalam kelompok yang mereka tangani
 * - Cek mentor_id untuk memastikan akses hanya untuk sesi/kelompok yang sesuai
 *
 * Flow:
 * 1. Mentor akses halaman daftar sesi
 * 2. Mentor bisa pilih kelompok dan buat sesi baru
 * 3. Mentor bisa lihat detail sesi dan isi absensi/laporan
 * 4. Mentor bisa hapus sesi jika diperlukan
 *
 * @package App\Http\Controllers\Mentor
 */
class SessionController extends Controller
{
    /**
     * Menampilkan daftar sesi mentoring milik mentor
     *
     * Proses:
     * 1. Query sessions yang dimiliki oleh kelompok milik mentor
     * 2. Eager load mentoringGroup untuk tampilan detail
     * 3. Urutkan berdasarkan tanggal (terbaru dulu)
     * 4. Paginate dengan 15 records per halaman
     * 5. Return view dengan daftar sesi
     *
     * Data retrieval:
     * - Gunakan whereHas('mentoringGroup') dengan filter mentor_id
     * - Eager load 'mentoringGroup' untuk tampilan detail
     * - Gunakan orderBy('date', 'desc') untuk urutkan dari terbaru
     *
     * @return \Illuminate\View\View View daftar sesi mentoring untuk mentor
     */
    public function index()
    {
        // Query sessions yang dimiliki oleh kelompok milik mentor
        $sessions = Session::whereHas('mentoringGroup', function($query) {
                $query->where('mentor_id', auth()->id());
            })
            ->with('mentoringGroup')  // Eager load mentoringGroup
            ->orderBy('date', 'desc')  // Urutkan dari terbaru
            ->paginate(15);  // Paginate 15 per halaman

        // Return view dengan daftar sesi
        return view('mentor.sessions.index', compact('sessions'));
    }

    /**
     * Menampilkan detail sesi mentoring
     *
     * Proses:
     * 1. Session di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Jika tidak authorized, abort(403) - forbidden
     * 4. Load relationships: mentoringGroup.members, attendances, progressReports
     * 5. Siapkan data attendances dan progressReports untuk akses mudah di view
     * 6. Return view dengan detail sesi dan data absensi/laporan
     *
     * Authorization:
     * - Cek $session->mentoringGroup->mentor_id === Auth::id()
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * Data preparation:
     * - KeyBy('mentee_id') untuk attendances dan progressReports
     * - Ini untuk akses cepat berdasarkan ID mentee di view
     *
     * @param Session $session Session model via route binding
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response View detail sesi atau 403 forbidden
     */
    public function show(Session $session)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Load relationships yang diperlukan untuk tampilan detail
        $session->load('mentoringGroup.members', 'attendances', 'progressReports');

        // Siapkan data untuk akses mudah di view
        $attendances = $session->attendances->keyBy('mentee_id');
        $progressReports = $session->progressReports->keyBy('mentee_id');

        // Return view dengan detail sesi dan data absensi/laporan
        return view('mentor.sessions.show', compact('session', 'attendances', 'progressReports'));
    }

    /**
     * Menampilkan form untuk memilih kelompok sebelum membuat sesi
     *
     * Proses:
     * 1. Query semua kelompok yang dimiliki mentor
     * 2. Return view dengan daftar kelompok
     *
     * Data retrieval:
     * - Gunakan where('mentor_id', auth()->id()) untuk filter kelompok milik mentor
     *
     * @return \Illuminate\View\View View form untuk memilih kelompok
     */
    public function selectGroupForSession()
    {
        // Query semua kelompok yang dimiliki mentor
        $groups = MentoringGroup::where('mentor_id', auth()->id())->get();

        // Return view dengan daftar kelompok
        return view('mentor.sessions.select-group', compact('groups'));
    }

    /**
     * Menampilkan form untuk membuat sesi baru untuk kelompok tertentu
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Jika tidak authorized, abort(403) - forbidden
     * 4. Return view dengan data kelompok
     *
     * Authorization:
     * - Cek $group->mentor_id === Auth::id() untuk memastikan akses hanya untuk kelompok yang sesuai
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * @param MentoringGroup $group MentoringGroup model via route binding
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response View form create sesi atau 403 forbidden
     */
    public function create(MentoringGroup $group)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($group->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Return view dengan data kelompok
        return view('mentor.sessions.create', compact('group'));
    }

    /**
     * Menyimpan sesi baru ke database
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Validasi input:
     *    - title: required, string, max 255
     *    - date: required, date format
     *    - description: optional, string
     * 4. Hitung nomor sesi berikutnya berdasarkan sesi-sesi sebelumnya
     * 5. Create session baru dengan data yang divalidasi
     * 6. Redirect ke halaman detail kelompok dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - date: required, date
     * - description: nullable, string
     *
     * Session numbering:
     * - Ambil session_number maksimum dari sesi-sesi sebelumnya
     * - Tambahkan 1 untuk mendapatkan nomor sesi berikutnya
     *
     * @param Request $request Form request dengan data sesi
     * @param MentoringGroup $group MentoringGroup model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke detail kelompok dengan success message
     */
    public function store(Request $request, MentoringGroup $group)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($group->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Hitung nomor sesi berikutnya berdasarkan sesi-sesi sebelumnya
        $lastSessionNumber = $group->sessions()->max('session_number') ?? 0;
        $nextSessionNumber = $lastSessionNumber + 1;

        // Create session baru dengan data yang divalidasi
        $group->sessions()->create([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'session_number' => $nextSessionNumber,
        ]);

        // Redirect ke halaman detail kelompok dengan success message
        return redirect()->route('mentor.groups.show', $group)->with('success', 'Sesi baru berhasil dibuat.');
    }

    /**
     * Memperbarui absensi dan laporan perkembangan mentee
     *
     * Proses:
     * 1. Session di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Validasi input:
     *    - attendances: required, array
     *    - attendances.*.status: required, in: present, absent, excused
     *    - reports: optional, array
     *    - reports.*.score: optional, numeric, min 0, max 100
     *    - reports.*.reading_notes: optional, string, max 1000
     * 4. Process attendances: updateOrCreate untuk setiap mentee
     * 5. Process progress reports: updateOrCreate jika ada score atau notes
     * 6. Redirect ke halaman detail sesi dengan success message
     *
     * Validasi (inline):
     * - attendances: required, array
     * - attendances.*.status: required, in:present,absent,excused
     * - reports: sometimes, array
     * - reports.*.score: nullable, numeric, min:0, max:100
     * - reports.*.reading_notes: nullable, string, max:1000
     *
     * Data processing:
     * - Gunakan updateOrCreate untuk attendances dan progress reports
     * - Hanya buat/update progress report jika ada score atau notes
     *
     * @param Request $request Form request dengan data absensi dan laporan
     * @param Session $session Session model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke detail sesi dengan success message
     */
    public function update(Request $request, Session $session)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Validasi input
        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', 'in:present,absent,excused'],
            'reports' => ['sometimes', 'array'],
            'reports.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reports.*.reading_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Process Attendances
        foreach ($validated['attendances'] as $menteeId => $data) {
            Attendance::updateOrCreate(
                ['session_id' => $session->id, 'mentee_id' => $menteeId],
                ['status' => $data['status']]
            );
        }

        // Process Progress Reports
        if (isset($validated['reports'])) {
            foreach ($validated['reports'] as $menteeId => $data) {
                // Hanya buat/update report jika ada score atau notes
                if (!empty($data['score']) || !empty($data['reading_notes'])) {
                    ProgressReport::updateOrCreate(
                        ['session_id' => $session->id, 'mentee_id' => $menteeId],
                        [
                            'score' => $data['score'],
                            'reading_notes' => $data['reading_notes'],
                        ]
                    );
                }
            }
        }

        // Redirect ke halaman detail sesi dengan success message
        return redirect()->route('mentor.sessions.show', $session)->with('success', 'Data absensi dan laporan berhasil disimpan.');
    }

    /**
     * Menghapus sesi mentoring
     *
     * Proses:
     * 1. Session di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Jika tidak authorized, abort(403) - forbidden
     * 4. Delete session dari database
     * 5. Redirect ke halaman daftar sesi dengan success message
     *
     * Authorization:
     * - Cek $session->mentoringGroup->mentor_id === Auth::id()
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * @param Session $session Session model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke daftar sesi dengan success message
     */
    public function destroy(Session $session)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Delete session dari database
        $session->delete();

        // Redirect ke halaman daftar sesi dengan success message
        return redirect()->route('mentor.sessions.index')->with('success', 'Sesi berhasil dihapus.');
    }
}
