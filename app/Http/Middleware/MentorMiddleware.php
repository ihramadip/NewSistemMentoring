<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MentorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role->name === 'Admin') {
            return $next($request);
        }

        if (!auth()->check() || auth()->user()->role->name !== 'Mentor') {
            return redirect('/'); // Or abort(403);
        }
        return $next($request);
    }
}
