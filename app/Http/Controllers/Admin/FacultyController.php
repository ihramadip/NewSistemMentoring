<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * FacultyController
 *
 * Controller untuk manage fakultas (MODULE D: Admin Dashboard & Sistem Data)
 * Admin dapat create, read, update, delete data fakultas
 *
 * Fitur:
 * - Index: list semua fakultas
 * - Create: show form untuk create fakultas baru
 * - Store: save fakultas baru, validate name unique
 * - Show: redirect ke index (not used for simple CRUD)
 * - Edit: show form untuk edit fakultas
 * - Update: update fakultas, validate name unique (except current)
 * - Delete: hapus fakultas
 *
 * Relationship:
 * - Faculty.users (hasMany): user-user yang ke-assign ke fakultas ini
 * - Setiap mentee harus ter-assign ke 1 fakultas
 * - Digunakan untuk grouping mentee saat auto-grouping
 *
 * Flow:
 * 1. Admin setup fakultas master data (create/update/delete)
 * 2. Admin assign mentee ke fakultas saat mentee import/create
 * 3. Auto-grouping groupBy faculty (chart shows mentees per faculty)
 * 4. Mentoring group dapat di-assign per faculty
 *
 * @package App\Http\Controllers\Admin
 */
class FacultyController extends Controller
{
    /**
     * Menampilkan list semua fakultas
     *
     * Proses:
     * 1. Query semua faculties dari database
     * 2. Return view dengan list faculties
     *
     * @return \Illuminate\View\View View list semua fakultas
     */
    public function index()
    {
        // Fetch semua faculties dari database
        $faculties = Faculty::all();

        // Return view dengan faculties list
        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Menampilkan form untuk create fakultas baru
     *
     * Proses:
     * 1. Return create view dengan empty form
     *
     * @return \Illuminate\View\View View form create fakultas
     */
    public function create()
    {
        // Return create view
        return view('admin.faculties.create');
    }

    /**
     * Menyimpan fakultas baru ke database
     *
     * Proses:
     * 1. Validasi input: name required, string, max 255, unique di faculties table
     * 2. Create Faculty record dengan name dari request
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:faculties
     * - Memastikan tidak ada duplikat nama fakultas
     *
     * @param Request $request Form request dengan name input
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:faculties'],
        ]);

        // Create new faculty record
        Faculty::create([
            'name' => $request->name,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Fakultas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail fakultas
     *
     * Note:
     * - Method ini tidak di-use untuk simple CRUD fakultas
     * - Kept untuk maintain RESTful resource controller structure
     * - Jika diakses, langsung redirect ke index
     *
     * @param Faculty $faculty Faculty model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index
     */
    public function show(Faculty $faculty)
    {
        // Not used for this simple CRUD, redirect ke index
        return redirect()->route('admin.faculties.index');
    }

    /**
     * Menampilkan form untuk edit fakultas
     *
     * Proses:
     * 1. Faculty di-resolve via route model binding
     * 2. Return edit view dengan faculty data untuk populate form
     *
     * @param Faculty $faculty Faculty model via route binding
     * @return \Illuminate\View\View View form edit fakultas
     */
    public function edit(Faculty $faculty)
    {
        // Return edit view dengan faculty data
        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Memperbarui fakultas di database
     *
     * Proses:
     * 1. Validasi input: name required, string, max 255, unique (except current fakultas)
     * 2. Update Faculty record dengan name baru
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:faculties (ignore current id)
     * - Rule::unique('faculties')->ignore($faculty->id) untuk exclude current faculty dari unique check
     * - Memastikan tidak ada duplikat nama, tapi boleh update ke nama lama
     *
     * @param Request $request Form request dengan name input
     * @param Faculty $faculty Faculty model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, Faculty $faculty)
    {
        // Validasi input dengan unique exception untuk current faculty
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
        ]);

        // Update faculty record
        $faculty->update([
            'name' => $request->name,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Fakultas berhasil diperbarui.');
    }

    /**
     * Menghapus fakultas dari database
     *
     * Proses:
     * 1. Faculty di-resolve via route model binding
     * 2. Delete faculty record
     * 3. Redirect ke index dengan success message
     *
     * WARNING:
     * - Jika ada users ter-assign ke fakultas ini, delete akan fail
     * - Implement check: check apakah ada users sebelum allow delete
     * - Atau set foreign key CASCADE DELETE (not recommended)
     * - Atau soft delete fakultas (recommended)
     *
     * @param Faculty $faculty Faculty model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(Faculty $faculty)
    {
        // Delete faculty record
        $faculty->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Fakultas berhasil dihapus.');
    }
}
