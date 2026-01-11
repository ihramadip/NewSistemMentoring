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

        $announcements = Announcement::whereNotNull('published_at')
                                    ->where('published_at', '<=', Carbon::now())
                                    ->orderBy('published_at', 'desc')
                                    ->get();

        return view('mentee.announcements.index', compact('announcements'));
    }
}
