<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * MaterialController
 *
 * Controller untuk manage materi pembelajaran per level (MODULE D: Admin Dashboard & Sistem Data)
 * Admin dapat create, read, update, delete materi pembelajaran untuk setiap level
 *
 * Fitur:
 * - Index: list semua materi dengan level info, sorted latest first
 * - Create: show form untuk upload materi baru (pilih level, file)
 * - Store: save materi baru, upload file ke storage, store file_path
 * - Edit: show form untuk edit materi, current file dapat di-replace
 * - Update: update materi info, handle file replacement atau keep old file
 * - Delete: hapus materi, delete file dari storage
 *
 * File handling:
 * - Accepted mimes: pdf, doc, docx, ppt, pptx (document & presentation)
 * - Max size: 10MB per file
 * - Storage path: public/materials/ (accessible via browser)
 * - On delete: cascade delete file dari storage
 * - On update: delete old file, store new file (atau keep old jika tidak di-upload)
 *
 * Relationship:
 * - Material.level (belongsTo): level yang material ini untuk
 * - Level.materials (hasMany): materi-materi untuk level tertentu
 * - Setiap material ter-assign ke 1 level
 *
 * Flow:
 * 1. Admin setup levels (LevelController)
 * 2. Admin upload materi per level (create/store)
 * 3. Mentor access materi untuk teach mentoring groups
 * 4. Mentee access materi untuk study
 *
 * @package App\Http\Controllers\Admin
 */
class MaterialController extends Controller
{
    /**
     * Menampilkan list semua materi
     *
     * Proses:
     * 1. Query semua materials dengan eager load level relationship
     * 2. Order by latest (created_at desc)
     * 3. Return view dengan list materials
     *
     * Eager loading:
     * - Material.level untuk display level name di list
     * - Mencegah N+1 query problem
     *
     * @return \Illuminate\View\View View list semua materi dengan level info
     */
    public function index()
    {
        // Query semua materials dengan eager load level, order latest first
        $materials = Material::with('level')->latest()->get();

        // Return view dengan materials list
        return view('admin.materials.index', compact('materials'));
    }

    /**
     * Menampilkan form untuk create materi baru
     *
     * Proses:
     * 1. Fetch semua levels dari database
     * 2. Return create view dengan levels dropdown
     *
     * Data:
     * - levels: untuk dropdown pemilihan level materi
     * - Admin dapat upload materi untuk level tertentu
     *
     * @return \Illuminate\View\View View form create materi
     */
    public function create()
    {
        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Return create view dengan levels
        return view('admin.materials.create', compact('levels'));
    }

    /**
     * Menyimpan materi baru ke database
     *
     * Proses:
     * 1. Validasi input:
     *    - title: required, string, max 255
     *    - description: optional, string
     *    - level_id: required, exists in levels table
     *    - file: required, file, mimes (pdf/doc/docx/ppt/pptx), max 10MB
     * 2. Store file ke storage/public/materials/
     * 3. Create Material record dengan title, description, level_id, file_path
     * 4. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - description: nullable, string
     * - level_id: required, exists:levels,id (foreign key check)
     * - file: required, file, mimes:pdf,doc,docx,ppt,pptx, max:10240 (10MB)
     *
     * File storage:
     * - Path: public/materials/ (via Storage::store)
     * - Returns: filePath seperti "public/materials/filename.pdf"
     * - File accessible via browser route (if configured)
     *
     * @param Request $request Form request dengan title, description, level_id, file
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_id' => ['required', 'exists:levels,id'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
        ]);

        // Store file ke public/materials/ directory
        $filePath = $request->file('file')->store('public/materials');

        // Create Material record
        Material::create([
            'title' => $request->title,
            'description' => $request->description,
            'level_id' => $request->level_id,
            'file_path' => $filePath,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.materials.index')
                        ->with('success', 'Materi berhasil diunggah.');
    }

    /**
     * Menampilkan form untuk edit materi
     *
     * Proses:
     * 1. Material di-resolve via route model binding
     * 2. Fetch semua levels untuk dropdown
     * 3. Return edit view dengan material & levels data
     *
     * Data:
     * - material: current materi untuk populate form
     * - levels: dropdown untuk change level assignment
     * - Admin dapat replace file atau keep existing file
     *
     * @param Material $material Material model via route binding
     * @return \Illuminate\View\View View form edit materi
     */
    public function edit(Material $material)
    {
        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Return edit view dengan material & levels
        return view('admin.materials.edit', compact('material', 'levels'));
    }

    /**
     * Memperbarui materi di database
     *
     * Proses:
     * 1. Validasi input:
     *    - title: required, string, max 255
     *    - description: optional, string
     *    - level_id: required, exists in levels table
     *    - file: optional, file, mimes (pdf/doc/docx/ppt/pptx), max 10MB
     * 2. Check apakah ada file baru di-upload
     * 3. Jika ada file baru:
     *    - Delete old file dari storage
     *    - Store new file ke storage/public/materials/
     * 4. Jika tidak ada file baru:
     *    - Keep old file_path (tidak ada perubahan)
     * 5. Update Material record
     * 6. Redirect ke index dengan success message
     *
     * File handling logic:
     * - Start dengan filePath = material->file_path (existing)
     * - Check $request->hasFile('file') untuk detect upload baru
     * - Jika ada upload: delete old file, store new file, update filePath
     * - Jika tidak ada upload: keep existing filePath
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - description: nullable, string
     * - level_id: required, exists:levels,id
     * - file: nullable, file, mimes:pdf,doc,docx,ppt,pptx, max:10240
     *
     * @param Request $request Form request dengan title, description, level_id, optional file
     * @param Material $material Material model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, Material $material)
    {
        // Validasi input
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_id' => ['required', 'exists:levels,id'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
        ]);

        // Start dengan existing filePath
        $filePath = $material->file_path;

        // Check jika ada file baru di-upload
        if ($request->hasFile('file')) {
            // Delete old file dari storage jika ada
            if ($material->file_path) {
                Storage::delete($material->file_path);
            }

            // Store new file ke public/materials/
            $filePath = $request->file('file')->store('public/materials');
        }

        // Update Material record dengan new data
        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'level_id' => $request->level_id,
            'file_path' => $filePath,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.materials.index')
                        ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Menghapus materi dari database
     *
     * Proses:
     * 1. Material di-resolve via route model binding
     * 2. Check apakah ada file_path
     * 3. Delete file dari storage (jika ada)
     * 4. Delete Material record dari database
     * 5. Redirect ke index dengan success message
     *
     * File deletion:
     * - Use Storage::delete() untuk remove dari public/materials/
     * - Only delete jika material->file_path not null/empty
     * - Jika file tidak ada, tidak perlu throw error
     *
     * @param Material $material Material model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(Material $material)
    {
        // Delete file dari storage jika ada
        if ($material->file_path) {
            Storage::delete($material->file_path);
        }

        // Delete Material record
        $material->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.materials.index')
                        ->with('success', 'Materi berhasil dihapus.');
    }
}
