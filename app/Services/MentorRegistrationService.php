<?php

namespace App\Services;

use App\Http\Requests\StoreMentorApplicationRequest;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * MentorRegistrationService
 *
 * Service untuk mengelola pendaftaran pementor baru (MODULE A: Mentor Management)
 * Menyediakan fungsi untuk menangani proses pendaftaran pementor termasuk
 * upload file dan pembuatan akun pengguna
 *
 * Fungsi utama:
 * - register(): Menangani pendaftaran pementor baru
 *
 * @package App\Services
 */
class MentorRegistrationService
{
    /**
     * Menangani pendaftaran aplikasi pementor baru
     *
     * Proses:
     * 1. Ambil role mentee dari database
     * 2. Mulai transaksi database
     * 3. Simpan file CV dan rekaman
     * 4. Buat akun pengguna baru dengan role mentee
     * 5. Buat aplikasi pementor dengan file yang diupload
     * 6. Commit transaksi jika berhasil, rollback jika gagal
     *
     * @param StoreMentorApplicationRequest $request Request yang telah divalidasi dari form pendaftaran
     * @return void
     * @throws \Exception Jika terjadi kesalahan saat proses pendaftaran
     */
    public function register(StoreMentorApplicationRequest $request): void
    {
        // Ambil role mentee dari database (akan digunakan sementara sebelum diverifikasi)
        $menteeRole = Role::where('name', 'mentee')->firstOrFail();

        // Mulai transaksi database untuk memastikan konsistensi data
        DB::beginTransaction();
        try {
            // Simpan file CV dan rekaman ke storage
            $cvPath = $request->file('cv')->store('private/cvs');
            $recordingPath = $request->file('recording')->store('private/recordings');

            // Buat akun pengguna baru dengan data dari request
            $user = User::create([
                'name' => $request->name,           // Nama pendaftar
                'npm' => $request->npm,             // NPM pendaftar
                'email' => $request->email,         // Email pendaftar
                'password' => Hash::make($request->password), // Hash password
                'faculty_id' => $request->faculty_id, // ID fakultas
                'gender' => $request->gender,       // Jenis kelamin
                'role_id' => $menteeRole->id,       // Role sementara (mentee) sebelum diverifikasi
            ]);

            // Buat aplikasi pementor dengan file yang diupload dan data histori BTAQ
            MentorApplication::create([
                'user_id' => $user->id,                 // ID pengguna yang dibuat
                'cv_path' => $cvPath,                   // Path file CV
                'recording_path' => $recordingPath,     // Path file rekaman
                'btaq_history' => $request->btaq_history, // Histori BTAQ
                'status' => 'pending',                  // Status awal aplikasi: pending
            ]);

            // Commit transaksi jika semua proses berhasil
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Log error untuk debugging
            Log::error('Mentor registration failed: ' . $e->getMessage());

            // Lempar ulang exception agar ditangani oleh controller
            throw $e;
        }
    }
}
