<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MentoringGroup;
use App\Models\User;

class MenteeGroupController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure the user is a mentee
        if ($user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Only mentees can view their group.');
        }

        // Get the mentoring group(s) the mentee is part of
        // Assuming a mentee belongs to only one active group for simplicity
        // If a mentee can be in multiple, this logic needs adjustment
        $mentoringGroup = $user->mentoringGroupsAsMentee()->with(['mentor', 'level', 'members'])->first();

        if (!$mentoringGroup) {
            return redirect()->route('dashboard')->with('warning', 'You have not been assigned to a mentoring group yet.');
        }

        return view('mentee.group.index', compact('mentoringGroup'));
    }
}
