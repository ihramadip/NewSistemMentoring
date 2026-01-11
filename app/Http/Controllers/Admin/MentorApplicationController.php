<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use App\Mail\MentorApplicationApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // For pathinfo_extension
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Added for file management

class MentorApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = MentorApplication::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10); // Paginate for better performance

        return view('admin.mentor-applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not directly used by admin, this is for public form.
        // If an admin needs to create one, it would be different.
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This would be handled by a public facing form controller.
        // Admin likely doesn't create applications directly.
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(MentorApplication $mentorApplication)
    {
        return view('admin.mentor-applications.show', compact('mentorApplication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MentorApplication $mentorApplication)
    {
        return view('admin.mentor-applications.edit', compact('mentorApplication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MentorApplication $mentorApplication)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'accepted', 'rejected'])],
            'notes_from_reviewer' => ['nullable', 'string'],
        ]);

        $originalStatus = $mentorApplication->status;

        $mentorApplication->update($request->only('status', 'notes_from_reviewer'));

        // If status is changed to 'accepted', update role and send email
        if ($request->status == 'accepted' && $originalStatus != 'accepted') {
            $mentorRole = \App\Models\Role::where('name', 'mentor')->first();
            if ($mentorRole) {
                $user = $mentorApplication->user;
                $user->role_id = $mentorRole->id;
                $user->save();

                // Send approval email
                Mail::to($user->email)->send(new MentorApplicationApproved($user));
            }
        }

        return redirect()->route('admin.mentor-applications.index')
                         ->with('success', 'Mentor application updated successfully.');
    }

    /**
     * Stream the audio recording for the specified application.
     */
    public function streamAudio(MentorApplication $mentorApplication)
    {
        $path = $mentorApplication->recording_path;
        
        // Early exit if path is null or empty
        if (empty($path)) {
            Log::error("Audio streaming failed: recording_path is null or empty for application ID {$mentorApplication->id}");
            abort(404, 'Audio file path is missing.');
        }

        Log::info("Attempting to stream audio from path: {$path} for application ID {$mentorApplication->id}");

        if (str_contains($path, '..')) {
            Log::warning("Audio streaming blocked due to path traversal attempt: {$path}");
            abort(403, 'Invalid path specified.');
        }

        if (!Storage::exists($path)) {
            Log::error("Audio file not found at path: {$path} for application ID {$mentorApplication->id}");
            abort(404, 'Audio file not found.');
        }

        $fileContents = Storage::get($path);
        $detectedMimeType = Storage::mimeType($path);
        $fileSize = Storage::size($path);
        $extension = Str::afterLast($path, '.');

        // Explicitly set MIME type for common audio formats
        $mimeType = match ($extension) {
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            default => $detectedMimeType ?: 'application/octet-stream',
        };
        
        Log::info("Streaming audio: Path={$path}, Exists=true, MIME={$mimeType}, Size={$fileSize} for application ID {$mentorApplication->id}");

        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $fileSize)
            ->header('Accept-Ranges', 'bytes');
    }

    /**
     * Stream the CV for the specified application.
     */
    public function streamCv(MentorApplication $mentorApplication)
    {
        $path = $mentorApplication->cv_path;
        
        // Early exit if path is null or empty
        if (empty($path)) {
            Log::error("CV streaming failed: cv_path is null or empty for application ID {$mentorApplication->id}");
            abort(404, 'CV file path is missing.');
        }

        Log::info("Attempting to stream CV from path: {$path} for application ID {$mentorApplication->id}");

        if (str_contains($path, '..')) {
            Log::warning("CV streaming blocked due to path traversal attempt: {$path}");
            abort(403, 'Invalid path specified.');
        }

        if (!Storage::exists($path)) {
            Log::error("CV file not found at path: {$path} for application ID {$mentorApplication->id}");
            abort(404, 'CV file not found.');
        }

        $fileContents = Storage::get($path);
        $mimeType = Storage::mimeType($path) ?: 'application/pdf'; // Default to application/pdf
        $fileSize = Storage::size($path);

        Log::info("Streaming CV: Path={$path}, Exists=true, MIME={$mimeType}, Size={$fileSize} for application ID {$mentorApplication->id}");

        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $fileSize)
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"'); // Suggest browser to display inline
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MentorApplication $mentorApplication)
    {
        // Optionally delete associated files from storage
        Storage::delete([$mentorApplication->cv_path, $mentorApplication->recording_path]);
        
        $mentorApplication->delete();

        return redirect()->route('admin.mentor-applications.index')
                         ->with('success', 'Mentor application deleted successfully.');
    }
}
