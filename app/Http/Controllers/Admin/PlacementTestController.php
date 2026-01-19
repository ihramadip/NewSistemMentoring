<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementTest;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * PlacementTestController
 *
 * Controller untuk mengelola hasil placement test mentee (MODULE B #3: Placement Test)
 * Admin dapat review hasil placement test, update skor & level assignment
 *
 * Fitur:
 * - Daftar hasil placement test semua mentee dengan pencarian & pagination
 * - View & edit detail placement test (update score & final level)
 * - Stream/download audio rekaman mentee
 * - Hapus placement test record
 *
 * @package App\Http\Controllers\Admin
 */
class PlacementTestController extends Controller
{
    /**
     * Menampilkan daftar semua hasil placement test dengan pagination & pencarian
     *
     * Fitur:
     * - Support pencarian by nama mentee atau NPM
     * - Pagination 20 items per halaman
     * - Eager load mentee & finalLevel
     * - Sort by NPM ascending
     *
     * @param Request $request HTTP request dengan optional 'search' parameter
     * @return \Illuminate\View\View View daftar placement test results
     */
    public function index(Request $request)
    {
        // Build query dari PlacementTest
        $query = PlacementTest::query()
            ->join('users', 'placement_tests.mentee_id', '=', 'users.id')
            ->select('placement_tests.*');

        // Jika ada search, filter by nama atau npm mentee
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                ->orWhere('users.npm', 'like', "%{$search}%");
            });
        }

        // Load relasi, urutkan by npm, paginate hasil
        $testResults = $query->with('mentee', 'finalLevel')
            ->orderBy('users.npm', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Return view dengan data testResults
        return view('admin.placement-tests.index', compact('testResults'));
    }

    /**
     * Method create - Not used for admin
     *
     * Hasil placement test tidak dibuat manual by admin
     * Hasil dibuat otomatis saat mentee submit tes (PlacementTestSubmissionController)
     *
     * @return void Abort 404
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Method store - Not used for admin
     *
     * Hasil placement test tidak disimpan manual by admin
     * Hasil disimpan otomatis saat mentee submit tes (PlacementTestSubmissionController)
     *
     * @param Request $request
     * @return void Abort 404
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Menampilkan detail placement test
     *
     * Redirect ke edit view (lebih useful untuk workflow ini)
     * Admin biasanya langsung mau edit hasil, bukan hanya melihat
     *
     * @param PlacementTest $placementTest Hasil tes (di-inject via route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect ke edit route
     */
    public function show(PlacementTest $placementTest)
    {
        // Redirect ke edit view untuk workflow yang lebih praktis
        return redirect()->route('admin.placement-tests.edit', $placementTest);
    }

    /**
     * Menampilkan form edit untuk hasil placement test
     *
     * Form untuk:
     * - Update audio_reading_score
     * - Update final_level_id (hasil penempatan)
     *
     * @param PlacementTest $placementTest Hasil tes (di-inject via route model binding)
     * @return \Illuminate\View\View View form edit placement test
     */
    public function edit(PlacementTest $placementTest)
    {
        // Ambil semua levels untuk dropdown
        $levels = Level::all();

        // Return view dengan data placementTest & levels
        return view('admin.placement-tests.edit', compact('placementTest', 'levels'));
    }

    /**
     * Menyimpan update hasil placement test (score & level)
     *
     * Proses:
     * 1. Validasi: audio_reading_score (0-100), final_level_id (exists)
     * 2. Update record placement test
     * 3. Redirect dengan pesan sukses
     *
     * Validasi:
     * - audio_reading_score: nullable, integer, min:0, max:100
     * - final_level_id: nullable, exists di levels table
     *
     * @param Request $request HTTP request dengan field audio_reading_score & final_level_id
     * @param PlacementTest $placementTest Hasil tes yang diupdate
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function update(Request $request, PlacementTest $placementTest)
    {
        // Validasi input
        $validatedData = $request->validate([
            'audio_reading_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'final_level_id' => ['nullable', 'exists:levels,id'],
        ]);

        // Update record
        $placementTest->update($validatedData);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.placement-tests.index')
                        ->with('success', 'Placement test result for ' . $placementTest->mentee->name . ' updated successfully.');
    }

    /**
     * Menghapus hasil placement test dari database
     *
     * Proses:
     * 1. Hapus record placement test
     * 2. Redirect dengan pesan sukses
     *
     * Note: Audio file tidak dihapus dari storage (opsional untuk audit trail)
     *
     * @param PlacementTest $placementTest Hasil tes yang dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function destroy(PlacementTest $placementTest)
    {
        // Hapus record
        $placementTest->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.placement-tests.index')
                        ->with('success', 'Placement test result deleted successfully.');
    }

    /**
     * Stream/download audio rekaman placement test mentee
     *
     * Fitur keamanan:
     * - Check jika audio file exists
     * - Abort 404 jika file tidak ditemukan
     *
     * @param PlacementTest $placementTest Hasil tes dengan audio_recording_path
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Response file untuk download
     */
    public function streamAudio(PlacementTest $placementTest)
    {
        // Validasi: file harus exist
        if (!$placementTest->audio_recording_path || !Storage::disk('local')->exists($placementTest->audio_recording_path)) {
            abort(404, 'Audio file not found.');
        }

        // Return file untuk streaming/download
        return Storage::disk('local')->response($placementTest->audio_recording_path);
    }
}
