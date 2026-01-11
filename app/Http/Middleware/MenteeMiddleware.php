<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenteeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If the user is a mentee or an admin, let them proceed
        if ($user && ($user->role->name === 'Mentee' || $user->role->name === 'Admin')) {
            return $next($request);
        }
        
        // If the user is authenticated but not a mentee, redirect them to their appropriate dashboard
        if ($user) {
            if ($user->role->name === 'Mentor') {
                return redirect()->route('mentor.dashboard')->with('error', 'Akses ditolak. Silakan gunakan dashboard mentor Anda.');
            }
        }

        // If not authenticated or role is unrecognized, redirect to home
        return redirect('/')->with('error', 'Akses tidak sah.');
    }
}
