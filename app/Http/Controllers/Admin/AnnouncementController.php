<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

/**
 * AnnouncementController
 *
 * Controller untuk manage pengumuman sistem (MODULE B #5: Announcement Management)
 * Admin dapat create, read, update, delete pengumuman untuk dikirim ke target audience
 *
 * Fitur:
 * - Index: list semua pengumuman dengan author info, sort by latest
 * - Create: show form untuk create pengumuman baru, input title, content, target_role
 * - Store: save pengumuman baru, validate required fields, set author & published_at
 * - Edit: show form untuk edit pengumuman existing
 * - Update: update pengumuman info (title, content, target_role)
 * - Delete: hapus pengumuman dari database
 *
 * Data structure:
 * - Announcement: author_id, title, content, target_role, published_at
 * - Relationship: author (belongsTo User via author_id)
 * - Target role: 'All', 'Admin', 'Mentor', 'Mentee' (untuk filter audience)
 *
 * Author management:
 * - author_id di-set ke Auth::id() (current logged-in admin)
 * - published_at di-set ke now() saat create (timestamp publish)
 * - Eager load author untuk display nama author di list
 *
 * Target audience:
 * - target_role: filter siapa yang bisa lihat pengumuman
 * - 'All': semua user (admin, mentor, mentee)
 * - 'Admin': hanya admin yang bisa lihat
 * - 'Mentor': hanya mentor yang bisa lihat
 * - 'Mentee': hanya mentee yang bisa lihat
 *
 * Flow:
 * 1. Admin create pengumuman baru (isi title, content, pilih target)
 * 2. Pengumuman disimpan dengan author & timestamp publish
 * 3. Pengumuman muncul di dashboard sesuai target audience
 * 4. Admin edit/hapus pengumuman jika perlu
 *
 * @package App\Http\Controllers\Admin
 */
class AnnouncementController extends Controller
{
    /**
     * Menampilkan list semua pengumuman
     *
     * Proses:
     * 1. Query semua announcements dengan eager load author
     * 2. Sort by latest (published_at desc)
     * 3. Return view dengan list announcements
     *
     * Eager loading:
     * - author: user yang buat pengumuman (belongsTo User)
     * - Untuk mencegah N+1 query problem di view (display author name)
     *
     * Sorting:
     * - latest(): alias untuk orderBy('published_at', 'desc')
     * - Pastikan pengumuman terbaru muncul di atas
     *
     * @return \Illuminate\View\View View list pengumuman
     */
    public function index()
    {
        // Query semua announcements dengan eager load author, sort by latest
        $announcements = Announcement::with(['author'])->latest()->get();

        // Return view dengan announcements list
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Menampilkan form untuk create pengumuman baru
     *
     * Proses:
     * 1. Return create view kosong untuk input pengumuman baru
     *
     * Form fields:
     * - title: judul pengumuman (required, max 255 chars)
     * - content: isi/konten pengumuman (required, text area)
     * - target_role: penerima pengumuman ('All', 'Admin', 'Mentor', 'Mentee')
     *
     * @return \Illuminate\View\View View form create pengumuman
     */
    public function create()
    {
        // Return create view kosong
        return view('admin.announcements.create');
    }

    /**
     * Menyimpan pengumuman baru ke database
     *
     * Proses:
     * 1. Validasi input:
     *    - title: required, string, max 255
     *    - content: required, string (no length limit)
     *    - target_role: optional, string (filter audience)
     * 2. Create Announcement record dengan validated data
     *    - author_id: set ke Auth::id() (current admin)
     *    - published_at: set ke now() (timestamp publish)
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - content: required, string
     * - target_role: nullable, string
     *
     * Auto-fields:
     * - author_id: diambil dari Auth::id() (admin yang login)
     * - published_at: di-set ke now() (waktu pengumuman dibuat)
     *
     * @param Request $request Form request dengan title, content, target_role
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'nullable|string',
        ]);

        // Create new announcement dengan author_id & published_at
        Announcement::create([
            'author_id' => Auth::id(), // Set author ke current admin
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'target_role' => $validatedData['target_role'] ?? null,
            'published_at' => now(), // Set timestamp publish
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.announcements.index')
                        ->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk edit pengumuman
     *
     * Proses:
     * 1. Announcement di-resolve via route model binding
     * 2. Return edit view dengan announcement data
     *
     * Data:
     * - announcement: current announcement untuk populate form
     *
     * Admin dapat:
     * - Change title, content, target_role
     * - Update info pengumuman tanpa buat baru
     *
     * @param Announcement $announcement Announcement model via route binding
     * @return \Illuminate\View\View View form edit pengumuman
     */
    public function edit(Announcement $announcement)
    {
        // Return edit view dengan announcement data
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Memperbarui pengumuman di database
     *
     * Proses:
     * 1. Validasi input:
     *    - title: required, string, max 255
     *    - content: required, string
     *    - target_role: optional, string
     * 2. Update Announcement record: title, content, target_role
     *    - published_at tidak di-update (tetap waktu publish asli)
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - content: required, string
     * - target_role: nullable, string
     *
     * Note:
     * - published_at tidak di-update (waktu publish tetap asli)
     * - author_id tidak di-update (tetap creator original)
     * - Hanya info pengumuman yang di-update
     *
     * @param Request $request Form request dengan pengumuman info
     * @param Announcement $announcement Announcement model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, Announcement $announcement)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'nullable|string',
        ]);

        // Update announcement info (tanpa ubah author & published_at)
        $announcement->update([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'target_role' => $validatedData['target_role'] ?? null,
        ]);

        // Redirect ke index dengan success message
        return redirect()->route('admin.announcements.index')
                        ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Menghapus pengumuman dari database
     *
     * Proses:
     * 1. Announcement di-resolve via route model binding
     * 2. Delete Announcement record
     * 3. Redirect ke index dengan success message
     *
     * Cascade deletion:
     * - Hanya announcement record yang di-delete
     * - Tidak ada foreign key references ke announcement
     * - Tidak mempengaruhi user lain
     *
     * WARNING:
     * - Pengumuman akan hilang sepenuhnya dari sistem
     * - Tidak ada soft delete atau archive mechanism saat ini
     * - Pastikan benar-benar ingin hapus sebelum konfirmasi
     *
     * @param Announcement $announcement Announcement model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(Announcement $announcement)
    {
        // Delete announcement record
        $announcement->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
