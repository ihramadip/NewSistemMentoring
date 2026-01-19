<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * LevelController
 *
 * Controller untuk manage level kemampuan Al-Quran (MODULE D: Admin Dashboard & Sistem Data)
 * Admin dapat create, read, update, delete data level kemampuan membaca Al-Quran
 *
 * Level tiers:
 * - Hijaiyah 1: Kenal huruf, belum lancar
 * - Hijaiyah 2: Sudah bisa membaca, masih terbata
 * - Ibtida (Pemula): Membaca dengan rules dasar
 * - Fasih (Lancar): Membaca dengan tajweed, lancar
 *
 * Fitur:
 * - Index: list semua levels dengan info materials & groups
 * - Create: show form untuk create level baru
 * - Store: save level baru, validate name unique, description optional
 * - Show: redirect ke index (not used)
 * - Edit: show form untuk edit level
 * - Update: update level, validate name unique (except current)
 * - Delete: hapus level
 *
 * Relationship:
 * - Level.materials (hasMany): materi-materi pembelajaran per level
 * - Level.mentoringGroups (hasMany): mentoring groups yang ke-assign ke level ini
 * - Level.exams (hasMany): ujian yang di-buat untuk level ini
 * - Setiap mentee ter-assign ke 1 level berdasarkan hasil placement test
 *
 * Flow:
 * 1. Admin setup levels (create/update/delete)
 * 2. Admin assign material ke level
 * 3. Mentee ambil placement test, di-assign ke level tertentu
 * 4. Mentee join mentoring group sesuai level
 * 5. Mentor teach level materials ke mentoring group
 *
 * @package App\Http\Controllers\Admin
 */
class LevelController extends Controller
{
    /**
     * Menampilkan list semua levels
     *
     * Proses:
     * 1. Query semua levels dari database
     * 2. Return view dengan list levels
     *
     * @return \Illuminate\View\View View list semua levels
     */
    public function index()
    {
        // Fetch semua levels dari database
        $levels = Level::all();

        // Return view dengan levels list
        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Menampilkan form untuk create level baru
     *
     * Proses:
     * 1. Return create view dengan empty form
     *
     * @return \Illuminate\View\View View form create level
     */
    public function create()
    {
        // Return create view
        return view('admin.levels.create');
    }

    /**
     * Menyimpan level baru ke database
     *
     * Proses:
     * 1. Validasi input:
     *    - name: required, string, max 255, unique di levels table
     *    - description: optional, string (deskripsi lengkap tentang level)
     * 2. Create Level record dengan all validated data
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:levels
     * - description: nullable, string (opsional)
     * - Memastikan tidak ada duplikat nama level
     *
     * @param Request $request Form request dengan name & optional description
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels'],
            'description' => ['nullable', 'string'],
        ]);

        // Create new level record dengan all validated data
        Level::create($request->all());

        // Redirect ke index dengan success message
        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail level
     *
     * Note:
     * - Method ini tidak di-use untuk simple CRUD level
     * - Kept untuk maintain RESTful resource controller structure
     * - Jika diakses, langsung redirect ke index
     *
     * @param Level $level Level model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index
     */
    public function show(Level $level)
    {
        // Not used for this simple CRUD, redirect ke index
        return redirect()->route('admin.levels.index');
    }

    /**
     * Menampilkan form untuk edit level
     *
     * Proses:
     * 1. Level di-resolve via route model binding
     * 2. Return edit view dengan level data untuk populate form
     *
     * @param Level $level Level model via route binding
     * @return \Illuminate\View\View View form edit level
     */
    public function edit(Level $level)
    {
        // Return edit view dengan level data
        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Memperbarui level di database
     *
     * Proses:
     * 1. Validasi input:
     *    - name: required, string, max 255, unique (except current level)
     *    - description: optional, string
     * 2. Update Level record dengan validated data
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:levels (ignore current id)
     * - description: nullable, string
     * - Rule::unique('levels')->ignore($level->id) untuk exclude current level dari unique check
     * - Memastikan tidak ada duplikat nama, tapi boleh update ke nama lama
     *
     * @param Request $request Form request dengan name & optional description
     * @param Level $level Level model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, Level $level)
    {
        // Validasi input dengan unique exception untuk current level
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('levels')->ignore($level->id)],
            'description' => ['nullable', 'string'],
        ]);

        // Update level record dengan validated data
        $level->update($request->all());

        // Redirect ke index dengan success message
        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level berhasil diperbarui.');
    }

    /**
     * Menghapus level dari database
     *
     * Proses:
     * 1. Level di-resolve via route model binding
     * 2. Delete level record
     * 3. Redirect ke index dengan success message
     *
     * WARNING:
     * - Jika ada mentoring groups ter-assign ke level ini, delete akan fail
     * - Jika ada materials ter-assign ke level ini, delete akan fail
     * - Implement check: verifikasi tidak ada dependent records sebelum allow delete
     * - Atau soft delete level (recommended untuk maintain history)
     * - Atau implement archive/deactivate logic
     *
     * @param Level $level Level model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(Level $level)
    {
        // Delete level record
        $level->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level berhasil dihapus.');
    }
}
