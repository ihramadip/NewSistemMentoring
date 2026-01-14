<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Http\Requests\StoreMentorApplicationRequest;
use App\Services\MentorRegistrationService;

class MentorRegistrationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faculties = Faculty::orderBy('name')->get();
        return view('mentor-registration.create', compact('faculties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMentorApplicationRequest  $request
     * @param  \App\Services\MentorRegistrationService  $registrationService
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMentorApplicationRequest $request, MentorRegistrationService $registrationService)
    {
        try {
            $registrationService->register($request);

            return redirect()->route('mentor.register.create')->with('success', 'Pendaftaran berhasil!');

        } catch (\Exception $e) {
            // The service logs the specific error, so we just need a generic message for the user.
            return back()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
        }
    }
}

