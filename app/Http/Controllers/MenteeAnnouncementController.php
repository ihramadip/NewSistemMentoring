<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Carbon\Carbon;

/**
 * MenteeAnnouncementController
 *
 * Controller untuk menampilkan pengumuman kepada mentee (MODULE C #5: Diskusi Terbuka)
 * Mentee dapat melihat pengumuman yang telah dipublish sesuai dengan target audience
 *
 * Fitur:
 * - Index: menampilkan list pengumuman yang telah dipublish
 *
 * Data structure:
 * - Announcement: author_id, title, content, target_role, published_at
 * - Filter: hanya pengumuman dengan published_at <= Carbon::now() yang ditampilkan
 * - Sorting: berdasarkan published_at desc (terbaru di atas)
 *
 * Audience filtering:
 * - Pengumuman ditampilkan berdasarkan target_role (All, Admin, Mentor, Mentee)
 * - Mentee hanya melihat pengumuman dengan target_role 'All' atau 'Mentee'
 *
 * Flow:
 * 1. Mentee akses halaman pengumuman
 * 2. Controller ambil pengumuman yang telah dipublish
 * 3. Filter berdasarkan waktu publish dan target audience
 * 4. Tampilkan pengumuman di view
 *
 * @package App\Http\Controllers
 */
class MenteeAnnouncementController extends Controller
{
    /**
     * Menampilkan list pengumuman untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login (mentee)
     * 2. Query announcements yang:
     *    - published_at tidak null (sudah dipublish)
     *    - published_at <= Carbon::now() (sudah waktunya tampil)
     *    - target_role = 'All' atau 'Mentee' (filter audience)
     * 3. Sort by published_at desc (pengumuman terbaru di atas)
     * 4. Return view dengan list announcements
     *
     * Filtering:
     * - whereNotNull('published_at'): hanya pengumuman yang sudah dipublish
     * - where('published_at', '<=', Carbon::now()): hanya pengumuman yang waktunya sudah tiba
     * - where('target_role', 'All') OR where('target_role', 'Mentee'): hanya pengumuman untuk mentee
     *
     * Sorting:
     * - orderBy('published_at', 'desc'): pengumuman terbaru muncul di atas
     *
     * @return \Illuminate\View\View View list pengumuman untuk mentee
     */
    public function index()
    {
        // Ambil user yang sedang login (mentee)
        $user = Auth::user();

        // Query announcements yang sudah dipublish dan waktunya sudah tiba
        // Filter juga berdasarkan target_role untuk mentee
        $announcements = Announcement::whereNotNull('published_at')
                                    ->where('published_at', '<=', Carbon::now())
                                    ->where(function($query) {
                                        $query->where('target_role', 'All')
                                              ->orWhere('target_role', 'Mentee');
                                    })
                                    ->orderBy('published_at', 'desc')
                                    ->get();

        // Return view dengan announcements list
        return view('mentee.announcements.index', compact('announcements'));
    }
}
