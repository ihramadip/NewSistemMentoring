<?php

namespace App\Http\Controllers;

use App\Models\PlacementTest;
use App\Models\PlacementTestDefinition;
use App\Http\Requests\StorePlacementTestRequest;
use App\Services\PlacementTestService;
use Illuminate\Support\Facades\Auth;

/**
 * PlacementTestSubmissionController
 *
 * Controller untuk proses placement test mentee (MODULE B #3: Placement Test)
 * Mentee dapat mengakses form tes & submit jawaban (audio + teori)
 *
 * Fitur:
 * - Tampilkan form placement test (soal + form upload audio)
 * - Validasi: mentee hanya bisa submit 1x (check existing record)
 * - Process submission dengan PlacementTestService
 * - Simpan audio recording & answers ke database
 * - Validasi form request melalui StorePlacementTestRequest
 *
 * Flow:
 * 1. Mentee akses route placement-test.create (show form)
 * 2. Check apakah sudah ada PlacementTest record
 * 3. Jika sudah ada, tampilkan halaman "sudah selesai"
 * 4. Jika belum, tampilkan form dengan semua soal & options
 * 5. Mentee submit form (POST placement-test.store)
 * 6. Service process: upload audio, simpan answers, hitung score, determine level
 *
 * @package App\Http\Controllers
 */
class PlacementTestSubmissionController extends Controller
{
    /**
     * Menampilkan form placement test untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login (authenticated mentee)
     * 2. Check apakah mentee sudah punya PlacementTest record (sudah test)
     * 3. Jika sudah ada, tampilkan halaman "completed"
     * 4. Jika belum, fetch PlacementTestDefinition dengan eager load questions.options
     * 5. Validasi: jika belum ada test definition (admin belum setup), abort 500
     * 6. Tampilkan form dengan semua soal & options
     *
     * Eager loading:
     * - PlacementTestDefinition.questions (soal-soal placement test)
     * - questions.options (pilihan jawaban untuk setiap soal)
     * - Ini untuk menghindari N+1 query problem di view
     *
     * @return \Illuminate\View\View View form placement test atau completed page
     */
    public function create()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Check: apakah user sudah punya PlacementTest record
        // Jika sudah, user sudah pernah submit test & tidak boleh submit lagi
        if (PlacementTest::where('mentee_id', $user->id)->exists()) {
            return view('placement-test.completed'); // Tampilkan halaman "sudah selesai"
        }

        // Fetch PlacementTestDefinition dengan eager load questions & options
        // Eager load untuk mencegah N+1 query problem saat render view
        $testDefinition = PlacementTestDefinition::with('questions.options')->first();

        // Validasi: harus ada placement test definition (admin harus setup dulu)
        if (!$testDefinition) {
            abort(500, "Placement test has not been configured by an administrator.");
        }

        // Return view form dengan semua soal
        return view('placement-test.create', ['questions' => $testDefinition->questions]);
    }

    /**
     * Menyimpan submission placement test mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Double-check: user belum boleh submit 2x (extra security layer)
     * 3. Validasi form via StorePlacementTestRequest
     * 4. Call PlacementTestService->handleSubmission() dengan:
     *    - user
     *    - answers (array jawaban soal)
     *    - audio_recording (file upload)
     * 5. Service handle: upload file, simpan PlacementTest & SubmissionAnswer, hitung score
     * 6. Service auto-determine level berdasarkan score
     * 7. Redirect ke dashboard dengan pesan sukses
     *
     * Error handling:
     * - Jika sudah ada PlacementTest record: redirect dengan error
     * - Jika exception saat service: back dengan error message
     *
     * Validasi (StorePlacementTestRequest):
     * - answers: required, array (setiap soal harus ada jawaban)
     * - audio_recording: required, file, mimes:audio/* (file audio)
     *
     * @param StorePlacementTestRequest $request Form request dengan validasi answers & audio
     * @param PlacementTestService $placementTestService Service untuk process submission
     * @return \Illuminate\Http\RedirectResponse Redirect ke dashboard dengan pesan
     */
    public function store(
        StorePlacementTestRequest $request,
        PlacementTestService $placementTestService
    ) {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Double-check: prevent re-submission (extra security layer)
        // User tidak boleh submit 2x meskipun somehow pass form submission
        if (PlacementTest::where('mentee_id', $user->id)->exists()) {
            return redirect()->route('dashboard')
                            ->with('error', 'You have already submitted your placement test.');
        }

        try {
            // Call service untuk handle submission
            // Service akan: upload audio, simpan answers, hitung score, determine level
            $placementTestService->handleSubmission(
                $user,
                $request->validated('answers'),     // Array jawaban soal dari form
                $request->file('audio_recording')   // File audio recording upload
            );

        } catch (\Exception $e) {
            // Jika ada exception, return back dengan error message
            return back()->with('error', $e->getMessage());
        }

        // Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')
                        ->with('success', 'Your placement test has been submitted successfully! Please wait for the results.');
    }
}
