<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Http\Requests\StoreMentorApplicationRequest;
use App\Services\MentorRegistrationService;

/**
 * MentorRegistrationController
 *
 * Controller untuk proses pendaftaran (oprec) calon pementor (MODULE A #1: Oprec Pementor)
 * Menampilkan form registrasi dan memproses data pendaftaran mentor
 *
 * Fitur:
 * - Menampilkan form pendaftaran dengan list fakultas
 * - Menerima upload CV, rekaman, dan data BTAQ
 * - Validasi data melalui StoreMentorApplicationRequest
 * - Menyimpan data ke database melalui MentorRegistrationService
 *
 * @package App\Http\Controllers
 */
class MentorRegistrationController extends Controller
{
    /**
     * Menampilkan form pendaftaran calon mentor
     *
     * Menampilkan form dengan dropdown list fakultas yang terurut alfabetis
     * Form berisi field: nama, email, CV, rekaman bacaan, BTAQ history, dll
     *
     * @return \Illuminate\View\View View form registrasi mentor dengan list fakultas
     */
    public function create()
    {
        // Ambil semua fakultas terurut alfabetis untuk dropdown
        $faculties = Faculty::orderBy('name')->get();

        // Return view dengan data faculties
        return view('mentor-registration.create', compact('faculties'));
    }

    /**
     * Menyimpan data pendaftaran mentor baru ke database
     *
     * Proses:
     * 1. Validasi input (dari StoreMentorApplicationRequest)
     * 2. Panggil MentorRegistrationService->register() untuk proses penyimpanan
     * 3. Service handle upload file (CV, rekaman), encrypt password, buat User & MentorApplication
     * 4. Redirect dengan pesan sukses atau error
     *
     * Validasi dilakukan oleh StoreMentorApplicationRequest (form request class)
     * Business logic dihandle oleh MentorRegistrationService (separation of concerns)
     *
     * @param StoreMentorApplicationRequest $request Request dengan validasi data pendaftaran
     * @param MentorRegistrationService $registrationService Service untuk proses registrasi
     * @return \Illuminate\Http\RedirectResponse Redirect ke form dengan pesan sukses/error
     */
    public function store(
        StoreMentorApplicationRequest $request,
        MentorRegistrationService $registrationService
    ) {
        try {
            // Panggil service untuk proses registrasi (upload, validate, save)
            $registrationService->register($request);

            // Redirect ke form dengan pesan sukses
            return redirect()->route('mentor.register.create')
                            ->with('success', 'Pendaftaran berhasil!');

        } catch (\Exception $e) {
            // Service sudah log error yang spesifik
            // Kita hanya tampilkan pesan generic ke user untuk security
            return back()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
        }
    }
}

