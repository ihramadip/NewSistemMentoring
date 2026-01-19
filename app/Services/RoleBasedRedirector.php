<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

/**
 * RoleBasedRedirector
 *
 * Service untuk mengarahkan pengguna ke dashboard yang sesuai berdasarkan peran mereka (MODULE D: Admin Dashboard)
 * Menyediakan fungsi untuk redirect otomatis berdasarkan role pengguna
 *
 * Fungsi utama:
 * - redirect(): Mengarahkan pengguna ke dashboard berdasarkan role mereka
 *
 * @package App\Services
 */
class RoleBasedRedirector
{
    /**
     * Mengarahkan pengguna ke dashboard yang sesuai berdasarkan peran mereka
     *
     * Proses:
     * 1. Definisikan rute dashboard berdasarkan role
     * 2. Ambil nama rute berdasarkan role pengguna
     * 3. Jika role tidak dikenali, gunakan rute default
     * 4. Redirect pengguna ke rute yang ditentukan atau ke intended destination
     *
     * @param  \App\Models\User  $user Pengguna yang akan diarahkan
     * @return \Illuminate\Http\RedirectResponse Respon redirect ke dashboard yang sesuai
     */
    public function redirect(User $user): RedirectResponse
    {
        // Definisikan rute dashboard berdasarkan role
        $roleRoutes = [
            'Admin' => 'admin.dashboard',    // Dashboard admin
            'Mentor' => 'mentor.dashboard',  // Dashboard mentor
        ];

        // Ambil nama rute berdasarkan role pengguna, gunakan 'dashboard' sebagai fallback
        $routeName = $roleRoutes[$user->role->name] ?? 'dashboard';

        // Redirect pengguna ke rute yang ditentukan atau ke intended destination
        return Redirect::intended(route($routeName, absolute: false));
    }
}
