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

        // Get the mentoring group(s) the mentee is part of
        // Assuming a mentee belongs to only one active group for simplicity
        // If a mentee can be in multiple, this logic needs adjustment
        $mentoringGroup = $user->mentoringGroupsAsMentee()->with(['mentor', 'level', 'members'])->first();

        if (!$mentoringGroup) {
            // For admins, we don't want to show this warning, just an empty state.
            // Let's check if the user is an admin.
            if ($user->role->name === 'Admin') {
                // Admin has no group, which is fine. Just show the page with no group data.
                return view('mentee.group.index', ['mentoringGroup' => null]);
            }
            return redirect()->route('dashboard')->with('warning', 'You have not been assigned to a mentoring group yet.');
        }

        return view('mentee.group.index', compact('mentoringGroup'));
    }
}
