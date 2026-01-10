<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentoringGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentorId = Auth::id();

        $groups = MentoringGroup::where('mentor_id', $mentorId)
                                ->with('level', 'members')
                                ->latest()
                                ->get();

        return view('mentor.groups.index', compact('groups'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MentoringGroup $group)
    {
        // Ensure the mentor is authorized to see this group
        if ($group->mentor_id !== Auth::id()) {
            abort(403);
        }

        $group->load('level', 'members', 'sessions.attendances');

        return view('mentor.groups.show', compact('group'));
    }
}
