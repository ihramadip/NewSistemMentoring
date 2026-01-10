<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Carbon\Carbon;

class MenteeAnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure the user is a mentee
        if ($user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Only mentees can view announcements.');
        }

        $announcements = Announcement::whereNotNull('published_at')
                                    ->where('published_at', '<=', Carbon::now())
                                    ->orderBy('published_at', 'desc')
                                    ->get();

        return view('mentee.announcements.index', compact('announcements'));
    }
}
