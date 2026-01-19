<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentoringGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * GroupController
 *
 * Controller untuk mengelola kelompok mentoring oleh mentor (MODULE A #6: Penugasan Kelompok)
 * Mentor dapat melihat daftar kelompok yang mereka tangani dan detail masing-masing kelompok
 *
 * Fitur:
 * - Index: menampilkan daftar kelompok mentoring yang ditangani mentor
 * - Show: menampilkan detail kelompok mentoring (anggota, sesi, kehadiran)
 *
 * Data structure:
 * - MentoringGroup: informasi kelompok (nama, jadwal, mentor, level)
 * - Level: tingkat kemampuan mentoring
 * - Members: daftar mentee dalam kelompok
 * - Sessions: daftar sesi mentoring dalam kelompok
 * - Attendances: informasi kehadiran mentee di sesi
 *
 * Authorization:
 * - Hanya mentor yang bisa mengakses kelompok yang mereka tangani
 * - Cek mentor_id untuk memastikan akses hanya untuk kelompok yang sesuai
 *
 * Flow:
 * 1. Mentor akses halaman daftar kelompok
 * 2. Controller tampilkan kelompok yang ditangani mentor
 * 3. Mentor bisa lihat detail masing-masing kelompok
 *
 * @package App\Http\Controllers\Mentor
 */
class GroupController extends Controller
{
    /**
     * Menampilkan daftar kelompok mentoring yang ditangani mentor
     *
     * Proses:
     * 1. Ambil ID mentor yang sedang login
     * 2. Query mentoring groups yang dimiliki mentor
     * 3. Eager load level dan members untuk tampilan detail
     * 4. Urutkan berdasarkan tanggal terbaru
     * 5. Return view dengan daftar kelompok
     *
     * Data retrieval:
     * - Gunakan where('mentor_id', $mentorId) untuk filter kelompok milik mentor
     * - Eager load ['level', 'members'] untuk tampilan detail
     * - Gunakan latest() untuk urutkan berdasarkan created_at terbaru
     *
     * @return \Illuminate\View\View View daftar kelompok mentoring untuk mentor
     */
    public function index()
    {
        // Ambil ID mentor yang sedang login
        $mentorId = Auth::id();

        // Query mentoring groups yang dimiliki mentor dengan eager load
        $groups = MentoringGroup::where('mentor_id', $mentorId)
                                ->with('level', 'members')  // Eager load level dan members
                                ->latest()  // Urutkan berdasarkan created_at terbaru
                                ->get();

        // Return view dengan daftar kelompok
        return view('mentor.groups.index', compact('groups'));
    }

    /**
     * Menampilkan detail kelompok mentoring
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Cek authorization: hanya mentor yang memiliki kelompok bisa akses
     * 3. Jika tidak authorized, abort(403) - forbidden
     * 4. Load level, members, dan sessions.attendances untuk tampilan detail
     * 5. Return view dengan detail kelompok
     *
     * Authorization:
     * - Cek $group->mentor_id === Auth::id() untuk memastikan akses hanya untuk kelompok yang sesuai
     * - Jika tidak match, abort(403) untuk mencegah unauthorized access
     *
     * Data loading:
     * - Gunakan $group->load() untuk eager load level, members, sessions.attendances
     * - Ini untuk menampilkan informasi detail kelompok di view
     *
     * @param MentoringGroup $group MentoringGroup model via route binding
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response View detail kelompok atau 403 forbidden
     */
    public function show(MentoringGroup $group)
    {
        // Cek authorization: hanya mentor yang memiliki kelompok bisa akses
        if ($group->mentor_id !== Auth::id()) {
            abort(403);  // Forbidden jika bukan mentor yang memiliki kelompok
        }

        // Load level, members, dan sessions.attendances untuk tampilan detail
        $group->load('level', 'members', 'sessions.attendances');

        // Return view dengan detail kelompok
        return view('mentor.groups.show', compact('group'));
    }
}
