<?php

namespace App\Http\Controllers;

use App\Models\MentoringGroup;
use App\Services\MenteeSessionService;
use Illuminate\Support\Facades\Auth;

class MenteeSessionController extends Controller
{
    protected $menteeSessionService;

    public function __construct(MenteeSessionService $menteeSessionService)
    {
        $this->menteeSessionService = $menteeSessionService;
    }

    public function index()
    {
        $user = Auth::user();

        // Admin view: Admins don't have a group, show an empty state.
        if ($user->role->name === 'Admin') {
            return view('mentee.sessions.index', [
                'sessions' => collect(), // Empty collection
                'additionalSessions' => collect(),
                'mentoringGroup' => null
            ]);
        }
        
        $sessionData = $this->menteeSessionService->getSessions($user);

        if (!$sessionData['mentoringGroup']) {
            return redirect()->route('dashboard')->with('warning', 'You have not been assigned to a mentoring group yet to view sessions.');
        }

        return view('mentee.sessions.index', $sessionData);
    }
}
