<?php

namespace App\Http\Controllers;

use App\Models\AdditionalSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * AdditionalSessionController
 *
 * Controller untuk manage sesi mentoring tambahan (MODULE C #2: Mentoring Tambahan 21 Pertemuan)
 * Mentee dapat create, read, update, delete sesi tambahan beyond 7 sesi wajib
 * Setiap sesi tambahan memerlukan bukti (foto/video/audio)
 *
 * Fitur:
 * - Index: redirect ke mentee sessions index (handled by MenteeSessionController)
 * - Create: show form untuk create sesi tambahan baru (cek limit 21 sesi)
 * - Store: save sesi tambahan baru, validate required fields & file upload
 * - Edit: show form untuk edit sesi tambahan existing
 * - Update: update sesi tambahan info (topic, date, status, proof)
 * - Delete: hapus sesi tambahan dari database & file storage
 *
 * Data structure:
 * - AdditionalSession: mentee_id, mentoring_group_id, topic, date, status, proof_path
 * - Limit: maksimal 21 sesi tambahan per mentee (konstanta MAX_SESSIONS)
 * - Proof: file bukti (foto/video/audio) di-upload ke storage
 *
 * Authorization:
 * - Hanya mentee yang bisa akses & manage sesi tambahan mereka sendiri
 * - Check via mentee_id === Auth::id() di edit, update, destroy
 *
 * File management:
 * - Upload proof ke 'public/proofs' directory
 * - Delete old proof file saat update atau delete
 * - Max file size: 2MB, format: jpg, png, jpeg
 *
 * Flow:
 * 1. Mentee create sesi tambahan (cek limit 21 sesi)
 * 2. Mentee input topic, date, status, upload proof
 * 3. Sesi tambahan muncul di dashboard mentee
 * 4. Mentee edit/hapus sesi tambahan jika perlu
 *
 * @package App\Http\Controllers
 */
class AdditionalSessionController extends Controller
{
    private const MAX_SESSIONS = 21;

    /**
     * Menampilkan list sesi tambahan (redirect ke mentee sessions index)
     *
     * Proses:
     * 1. Redirect ke route 'mentee.sessions.index' (handled by MenteeSessionController)
     * 2. Tidak ada implementasi spesifik di sini karena sudah ditangani oleh controller lain
     *
     * Note:
     * - Fungsi ini redirect ke index yang sudah ada di MenteeSessionController
     * - Untuk konsistensi tampilan dan pengelolaan semua jenis sesi
     *
     * @return \Illuminate\Http\RedirectResponse Redirect ke mentee sessions index
     */
    public function index()
    {
        // Redirect ke mentee sessions index karena sudah ditangani oleh MenteeSessionController
        return redirect()->route('mentee.sessions.index');
    }

    /**
     * Menampilkan form untuk create sesi tambahan baru
     *
     * Proses:
     * 1. Ambil mentee yang sedang login (Auth::user())
     * 2. Check jumlah sesi tambahan yang sudah dibuat
     * 3. Jika sudah mencapai MAX_SESSIONS (21), redirect dengan warning
     * 4. Jika belum mencapai limit, tampilkan form create
     *
     * Limit checking:
     * - Cek jumlah AdditionalSession dengan where('mentee_id', $mentee->id)
     * - Bandingkan dengan MAX_SESSIONS constant (21)
     * - Jika >= MAX_SESSIONS, redirect ke index dengan warning
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse View form create atau redirect jika limit
     */
    public function create()
    {
        // Ambil mentee yang sedang login
        $mentee = Auth::user();

        // Check jumlah sesi tambahan yang sudah dibuat
        $sessionsCount = AdditionalSession::where('mentee_id', $mentee->id)->count();

        // Check limit: jika sudah mencapai MAX_SESSIONS, redirect dengan warning
        if ($sessionsCount >= self::MAX_SESSIONS) {
            return redirect()->route('mentee.sessions.index')
                           ->with('warning', 'Anda sudah mencapai batas maksimal 21 sesi tambahan.');
        }

        // Return view form create
        return view('mentee.additional-sessions.create');
    }

    /**
     * Menyimpan sesi tambahan baru ke database
     *
     * Proses:
     * 1. Ambil mentee yang sedang login
     * 2. Ambil mentoring group mentee (untuk assign ke group)
     * 3. Check apakah mentee tergabung dalam group mentoring
     * 4. Check jumlah sesi tambahan (pastikan tidak melebihi limit)
     * 5. Validasi input:
     *    - topic: required, string, max 255
     *    - date: required, date format
     *    - status: required, in: sudah, belum
     *    - proof: optional, image (jpg, png, jpeg), max 2MB
     * 6. Upload proof file jika ada
     * 7. Create AdditionalSession record
     * 8. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - topic: required, string, max:255
     * - date: required, date
     * - status: required, in:sudah,belum
     * - proof: nullable, image, mimes:jpg,png,jpeg, max:2048
     *
     * File upload:
     * - Store ke 'public/proofs' directory
     * - Gunakan $request->file('proof')->store('public/proofs')
     *
     * @param Request $request Form request dengan sesi info & proof file
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success/error
     */
    public function store(Request $request)
    {
        // Ambil mentee yang sedang login
        $mentee = Auth::user();

        // Ambil mentoring group mentee (untuk assign ke group)
        $mentoringGroup = $mentee->mentoringGroupsAsMentee()->first();

        // Check: pastikan mentee tergabung dalam group mentoring
        if (!$mentoringGroup) {
            return redirect()->route('mentee.sessions.index')
                           ->with('error', 'Anda tidak tergabung dalam kelompok mentoring manapun.');
        }

        // Check jumlah sesi tambahan (pastikan tidak melebihi limit)
        $sessionsCount = AdditionalSession::where('mentee_id', $mentee->id)->count();
        if ($sessionsCount >= self::MAX_SESSIONS) {
            return redirect()->route('mentee.sessions.index')
                           ->with('warning', 'Anda sudah mencapai batas maksimal 21 sesi tambahan.');
        }

        // Validasi input
        $request->validate([
            'topic' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:sudah,belum',
            'proof' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Upload proof file jika ada
        $filePath = null;
        if ($request->hasFile('proof')) {
            $filePath = $request->file('proof')->store('public/proofs');
        }

        // Create new additional session
        AdditionalSession::create([
            'mentee_id' => $mentee->id,
            'mentoring_group_id' => $mentoringGroup->id,
            'topic' => $request->topic,
            'date' => $request->date,
            'status' => $request->status,
            'proof_path' => $filePath,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('mentee.sessions.index')
                        ->with('success', 'Sesi tambahan berhasil disimpan.');
    }

    /**
     * Menampilkan detail sesi tambahan (redirect ke index)
     *
     * Proses:
     * 1. Saat ini redirect ke index karena tidak ada show view spesifik
     * 2. Detail sesi tambahan biasanya ditampilkan di dashboard mentee
     *
     * Note:
     * - Fungsi ini redirect ke index karena tidak ada implementasi show view
     * - Untuk konsistensi tampilan dan pengelolaan semua jenis sesi
     *
     * @param AdditionalSession $additionalSession AdditionalSession model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index
     */
    public function show(AdditionalSession $additionalSession)
    {
        // Redirect ke index karena tidak ada show view spesifik
        return redirect()->route('mentee.sessions.index');
    }

    /**
     * Menampilkan form untuk edit sesi tambahan existing
     *
     * Proses:
     * 1. AdditionalSession di-resolve via route model binding
     * 2. Check authorization: hanya mentee yang punya sesi bisa edit
     * 3. Jika tidak authorized, abort(403) - forbidden
     * 4. Return edit view dengan additionalSession data
     *
     * Authorization:
     * - Check $additionalSession->mentee_id === Auth::id()
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * @param AdditionalSession $additionalSession AdditionalSession model via route binding
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response View form edit atau 403 forbidden
     */
    public function edit(AdditionalSession $additionalSession)
    {
        // Authorization check: hanya mentee yang punya sesi bisa edit
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }

        // Return edit view dengan additionalSession data
        return view('mentee.additional-sessions.edit', compact('additionalSession'));
    }

    /**
     * Memperbarui sesi tambahan di database
     *
     * Proses:
     * 1. AdditionalSession di-resolve via route model binding
     * 2. Check authorization: hanya mentee yang punya sesi bisa update
     * 3. Validasi input (sama seperti store):
     *    - topic: required, string, max 255
     *    - date: required, date format
     *    - status: required, in: sudah, belum
     *    - proof: optional, image (jpg, png, jpeg), max 2MB
     * 4. Handle proof file upload:
     *    - Jika ada file baru, delete old file
     *    - Upload new file ke storage
     * 5. Update AdditionalSession record
     * 6. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - topic: required, string, max:255
     * - date: required, date
     * - status: required, in:sudah,belum
     * - proof: nullable, image, mimes:jpg,png,jpeg, max:2048
     *
     * File management:
     * - Delete old file sebelum upload new file: Storage::delete($filePath)
     * - Store new file ke 'public/proofs' directory
     *
     * @param Request $request Form request dengan sesi info & proof file
     * @param AdditionalSession $additionalSession AdditionalSession model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, AdditionalSession $additionalSession)
    {
        // Authorization check: hanya mentee yang punya sesi bisa update
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }

        // Validasi input
        $request->validate([
            'topic' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:sudah,belum',
            'proof' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Handle proof file upload
        $filePath = $additionalSession->proof_path;
        if ($request->hasFile('proof')) {
            // Delete old file jika exists
            if ($filePath) {
                Storage::delete($filePath);
            }
            // Upload new file
            $filePath = $request->file('proof')->store('public/proofs');
        }

        // Update additional session info
        $additionalSession->update([
            'topic' => $request->topic,
            'date' => $request->date,
            'status' => $request->status,
            'proof_path' => $filePath,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('mentee.sessions.index')
                        ->with('success', 'Sesi tambahan berhasil diperbarui.');
    }

    /**
     * Menghapus sesi tambahan dari database
     *
     * Proses:
     * 1. AdditionalSession di-resolve via route model binding
     * 2. Check authorization: hanya mentee yang punya sesi bisa delete
     * 3. Delete file proof yang terkait dari storage
     * 4. Delete AdditionalSession record
     * 5. Redirect ke index dengan success message
     *
     * Authorization:
     * - Check $additionalSession->mentee_id === Auth::id()
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * File cleanup:
     * - Delete proof file dari storage sebelum delete record
     * - Gunakan Storage::delete($additionalSession->proof_path)
     *
     * @param AdditionalSession $additionalSession AdditionalSession model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(AdditionalSession $additionalSession)
    {
        // Authorization check: hanya mentee yang punya sesi bisa delete
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }

        // Delete file proof yang terkait dari storage
        if ($additionalSession->proof_path) {
            Storage::delete($additionalSession->proof_path);
        }

        // Delete additional session record
        $additionalSession->delete();

        // Redirect ke index dengan success message
        return redirect()->route('mentee.sessions.index')
                         ->with('success', 'Sesi tambahan berhasil dihapus.');
    }
}