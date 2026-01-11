<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use App\Models\MentorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:255', 'unique:users,npm'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'gender' => ['required', 'in:male,female'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:2048'], // 2MB Max
            'recording' => ['required', 'file', 'mimes:mp3,wav,m4a', 'max:10240'], // 10MB Max
            'btaq_history' => ['required', 'string', 'max:5000'],
        ]);

        $menteeRole = Role::where('name', 'mentee')->first();
        if (!$menteeRole) {
            // Handle case where 'mentee' role doesn't exist
            return back()->with('error', 'Default role not found. Please contact administrator.');
        }

        DB::beginTransaction();
        try {
            // Store files
            $cvPath = $request->file('cv')->store('private/cvs');
            $recordingPath = $request->file('recording')->store('private/recordings');

            // Create User
            $user = User::create([
                'name' => $request->name,
                'npm' => $request->npm,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'faculty_id' => $request->faculty_id,
                'gender' => $request->gender,
                'role_id' => $menteeRole->id,
            ]);

            // Create Mentor Application
            MentorApplication::create([
                'user_id' => $user->id,
                'cv_path' => $cvPath,
                'recording_path' => $recordingPath,
                'btaq_history' => $request->btaq_history,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('mentor.register.create')->with('success', 'Pendaftaran berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally log the error
            // Log::error('Mentor registration failed: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
        }
    }
}
