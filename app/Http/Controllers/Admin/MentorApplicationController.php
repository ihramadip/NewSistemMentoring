<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use Illuminate\Http\Request;
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

        $mentorApplication->update($request->all());

        return redirect()->route('admin.mentor-applications.index')
                         ->with('success', 'Mentor application updated successfully.');
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
