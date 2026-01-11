<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementTest;
use App\Models\Material;

class MenteeMaterialController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->role->name === 'Admin';

        // If user is neither Mentee nor Admin, deny access.
        if (!$isAdmin && $user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $materials = collect();
        $placementTest = null;

        if (!$isAdmin) { // This is a Mentee
            $placementTest = PlacementTest::where('mentee_id', $user->id)
                                        ->whereNotNull('final_level_id')
                                        ->first();

            if (!$placementTest) {
                return redirect()->route('dashboard')->with('warning', 'You need to complete your placement test and have a level assigned to view materials.');
            }

            $materials = Material::where('level_id', $placementTest->final_level_id)
                                 ->latest()
                                 ->get();
        } else { // This is an Admin
            // Admins see all materials, grouped by Level for the view
            $materials = Material::with('level')->latest()->get()->groupBy('level.name');
        }

        return view('mentee.materials.index', compact('materials', 'placementTest', 'isAdmin'));
    }
}
