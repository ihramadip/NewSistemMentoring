<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class RoleBasedRedirector
{
    /**
     * Redirect the user to the appropriate dashboard based on their role.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(User $user): RedirectResponse
    {
        $roleRoutes = [
            'Admin' => 'admin.dashboard',
            'Mentor' => 'mentor.dashboard',
        ];

        $routeName = $roleRoutes[$user->role->name] ?? 'dashboard';

        return Redirect::intended(route($routeName, absolute: false));
    }
}
