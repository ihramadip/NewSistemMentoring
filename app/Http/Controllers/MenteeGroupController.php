<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MentoringGroup;
use App\Models\User;

/**
 * MenteeGroupController
 *
 * Controller untuk menampilkan informasi kelompok mentoring kepada mentee (MODULE B #7: Pengelompokan Kelompok)
 * Mentee dapat melihat informasi kelompok mentoring mereka: mentor, level, anggota
 *
 * Fitur:
 * - Index: menampilkan informasi kelompok mentoring mentee
 *
 * Data structure:
 * - MentoringGroup: informasi kelompok (nama, jadwal, dll)
 * - Mentor: informasi mentor yang menangani kelompok
 * - Level: tingkat kemampuan mentoring
 * - Members: daftar anggota kelompok
 *
 * Authorization:
 * - Hanya mentee yang bisa melihat informasi kelompok mereka sendiri
 * - Admin juga bisa mengakses (untuk monitoring), tapi tidak memiliki kelompok
 *
 * Flow:
 * 1. Mentee akses halaman informasi kelompok
 * 2. Controller ambil informasi kelompok mentee
 * 3. Tampilkan informasi kelompok di view
 *
 * @package App\Http\Controllers
 */
class MenteeGroupController extends Controller
{
    /**
     * Menampilkan informasi kelompok mentoring untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Query mentoring group yang dimiliki user (mentoringGroupsAsMentee)
     * 3. Eager load mentor, level, dan members untuk tampilan detail
     * 4. Jika mentee tidak memiliki group:
     *    - Jika user adalah admin, tampilkan halaman dengan null group
     *    - Jika user adalah mentee, redirect ke dashboard dengan warning
     * 5. Jika mentee memiliki group, tampilkan informasi group di view
     *
     * Data retrieval:
     * - Gunakan user->mentoringGroupsAsMentee() untuk ambil group mentee
     * - Eager load ['mentor', 'level', 'members'] untuk tampilan detail
     * - Asumsi: mentee hanya memiliki satu active group
     *
     * Authorization handling:
     * - Cek role user untuk menentukan response jika tidak ada group
     * - Admin tidak memiliki group, ini normal untuk monitoring
     * - Mentee harus memiliki group untuk akses fitur ini
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirect ke dashboard jika tidak ada group atau view informasi group
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil mentoring group yang dimiliki mentee
        // Eager load mentor, level, dan members untuk tampilan detail
        $mentoringGroup = $user->mentoringGroupsAsMentee()
                              ->with(['mentor', 'level', 'members'])
                              ->first();

        // Jika mentee tidak memiliki group
        if (!$mentoringGroup) {
            // Jika user adalah admin, tampilkan halaman dengan null group
            // Admin tidak memiliki group, ini normal untuk monitoring
            if ($user->role->name === 'Admin') {
                return view('mentee.group.index', ['mentoringGroup' => null]);
            }

            // Jika user adalah mentee, redirect ke dashboard dengan warning
            return redirect()->route('dashboard')
                           ->with('warning', 'You have not been assigned to a mentoring group yet.');
        }

        // Jika mentee memiliki group, tampilkan informasi group di view
        return view('mentee.group.index', compact('mentoringGroup'));
    }
}
