<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementTest;
use App\Models\Material;

/**
 * MenteeMaterialController
 *
 * Controller untuk menampilkan materi pembelajaran kepada mentee (MODULE C #4: Materi Belajar Per Level)
 * Mentee dapat melihat materi sesuai dengan level yang ditentukan dari placement test
 *
 * Fitur:
 * - Index: menampilkan daftar materi pembelajaran berdasarkan level
 *
 * Data structure:
 * - Material: informasi materi (judul, deskripsi, file path, dll)
 * - Level: tingkat kemampuan mentoring
 * - PlacementTest: hasil tes penempatan untuk menentukan level mentee
 *
 * Authorization:
 * - Hanya mentee dan admin yang bisa mengakses materi
 * - Mentee hanya bisa melihat materi sesuai level mereka
 * - Admin bisa melihat semua materi
 *
 * Flow:
 * 1. Mentee akses halaman materi
 * 2. Controller cek level mentee dari placement test
 * 3. Tampilkan materi sesuai dengan level mentee
 *
 * @package App\Http\Controllers
 */
class MenteeMaterialController extends Controller
{
    /**
     * Menampilkan daftar materi pembelajaran untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Identifikasi apakah user adalah admin
     * 3. Cek authorization: hanya mentee dan admin yang bisa akses
     * 4. Jika user bukan mentee atau admin, redirect dengan error
     * 5. Jika user adalah mentee:
     *    - Ambil placement test untuk menentukan level
     *    - Cek apakah mentee sudah memiliki level dari placement test
     *    - Jika tidak ada level, redirect dengan warning
     *    - Ambil materi berdasarkan level dari placement test
     * 6. Jika user adalah admin:
     *    - Ambil semua materi dan group by level name
     * 7. Return view dengan materials, placementTest, dan isAdmin flag
     *
     * Authorization:
     * - Cek role user: hanya 'Mentee' atau 'Admin' yang bisa akses
     * - Jika bukan keduanya, redirect ke dashboard dengan error
     *
     * Level determination:
     * - Gunakan PlacementTest untuk menentukan level mentee
     * - Cek whereNotNull('final_level_id') untuk memastikan level sudah ditentukan
     *
     * Material filtering:
     * - Mentee: where('level_id', $placementTest->final_level_id)
     * - Admin: semua materi dengan groupBy('level.name')
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirect ke dashboard jika tidak authorized atau view daftar materi
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Identifikasi apakah user adalah admin
        $isAdmin = $user->role->name === 'Admin';

        // Cek authorization: hanya mentee dan admin yang bisa akses
        if (!$isAdmin && $user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $materials = collect();
        $placementTest = null;

        if (!$isAdmin) { // Ini adalah Mentee
            // Ambil placement test untuk menentukan level mentee
            $placementTest = PlacementTest::where('mentee_id', $user->id)
                                        ->whereNotNull('final_level_id')
                                        ->first();

            // Cek apakah mentee sudah memiliki level dari placement test
            if (!$placementTest) {
                return redirect()->route('dashboard')
                               ->with('warning', 'You need to complete your placement test and have a level assigned to view materials.');
            }

            // Ambil materi berdasarkan level dari placement test
            $materials = Material::where('level_id', $placementTest->final_level_id)
                                 ->latest()  // Urutkan terbaru dulu
                                 ->get();
        } else { // Ini adalah Admin
            // Admin melihat semua materi, grouped by Level untuk view
            $materials = Material::with('level')
                                ->latest()  // Urutkan terbaru dulu
                                ->get()
                                ->groupBy('level.name');  // Group by nama level
        }

        // Return view dengan semua data materi
        return view('mentee.materials.index', compact('materials', 'placementTest', 'isAdmin'));
    }
}
