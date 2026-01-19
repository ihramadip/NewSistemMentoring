<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * ProfileController
 *
 * Controller untuk mengelola profil pengguna (Admin, Mentor, Mentee)
 * Pengguna dapat melihat, mengedit, dan menghapus akun mereka sendiri
 *
 * Fitur:
 * - Edit: menampilkan form edit profil
 * - Update: memperbarui informasi profil pengguna
 * - Destroy: menghapus akun pengguna
 *
 * Data structure:
 * - User: informasi profil pengguna (nama, email, dll)
 *
 * Authorization:
 * - Hanya pengguna yang sedang login yang bisa mengakses profil mereka sendiri
 *
 * Flow:
 * 1. Pengguna akses halaman profil
 * 2. Controller tampilkan form edit profil
 * 3. Pengguna update informasi profil
 * 4. Pengguna bisa hapus akun mereka sendiri
 *
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil pengguna
     *
     * Proses:
     * 1. Ambil user dari request (pengguna yang sedang login)
     * 2. Return view edit profil dengan data user
     *
     * Authorization:
     * - Hanya pengguna yang sedang login yang bisa mengakses
     * - Laravel Fortify/Laravel Breeze handle otentikasi
     *
     * @param Request $request Request object yang berisi user yang sedang login
     * @return View View form edit profil
     */
    public function edit(Request $request): View
    {
        // Return view edit profil dengan data user yang sedang login
        return view('profile.edit', [
            'user' => $request->user(),  // Ambil user dari request
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna
     *
     * Proses:
     * 1. Validasi input menggunakan ProfileUpdateRequest
     * 2. Fill user dengan data yang telah divalidasi
     * 3. Jika email berubah (isDirty), set email_verified_at ke null
     * 4. Save perubahan ke database
     * 5. Redirect ke halaman edit profil dengan status sukses
     *
     * Validasi:
     * - Gunakan ProfileUpdateRequest untuk validasi input
     * - Validasi biasanya mencakup: nama, email (unique jika berubah), dll
     *
     * Email verification handling:
     * - Jika email berubah, set email_verified_at = null
     * - Ini untuk memastikan email baru diverifikasi
     *
     * @param ProfileUpdateRequest $request Request dengan data profil yang telah divalidasi
     * @return RedirectResponse Redirect ke halaman edit profil dengan status sukses
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Fill user dengan data yang telah divalidasi
        $request->user()->fill($request->validated());

        // Jika email berubah, set email_verified_at ke null
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save perubahan ke database
        $request->user()->save();

        // Redirect ke halaman edit profil dengan status sukses
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun pengguna
     *
     * Proses:
     * 1. Validasi password menggunakan validateWithBag
     * 2. Ambil user dari request
     * 3. Logout user dari sesi saat ini
     * 4. Delete user dari database
     * 5. Invalidate session dan regenerate token
     * 6. Redirect ke halaman utama
     *
     * Validation:
     * - Gunakan validateWithBag('userDeletion') untuk validasi password
     * - Password harus sesuai dengan current_password rule
     *
     * Security:
     * - Validasi password untuk mencegah penghapusan akun tanpa izin
     * - Logout sebelum delete untuk keamanan
     * - Invalidate session dan regenerate token setelah delete
     *
     * @param Request $request Request object yang berisi user yang akan dihapus
     * @return RedirectResponse Redirect ke halaman utama setelah penghapusan
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password untuk konfirmasi penghapusan akun
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Ambil user dari request
        $user = $request->user();

        // Logout user dari sesi saat ini
        Auth::logout();

        // Delete user dari database
        $user->delete();

        // Invalidate session dan regenerate token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama
        return Redirect::to('/');
    }
}
