<?php

namespace App\Services;

use App\Http\Requests\StoreMentorApplicationRequest;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MentorRegistrationService
{
    /**
     * Handle the registration of a new mentor application.
     *
     * @param StoreMentorApplicationRequest $request
     * @return void
     * @throws \Exception
     */
    public function register(StoreMentorApplicationRequest $request): void
    {
        $menteeRole = Role::where('name', 'mentee')->firstOrFail();

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
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Mentor registration failed: ' . $e->getMessage());

            // Re-throw the exception to be handled by the controller
            throw $e;
        }
    }
}
