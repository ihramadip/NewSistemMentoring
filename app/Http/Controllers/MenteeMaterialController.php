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

        // Ensure the user is a mentee and has completed a placement test
        if ($user->role->name !== 'Mentee') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Only mentees can view materials.');
        }

        $placementTest = PlacementTest::where('mentee_id', $user->id)
                                    ->whereNotNull('final_level_id')
                                    ->first();

        if (!$placementTest) {
            return redirect()->route('dashboard')->with('warning', 'You need to complete your placement test and have a level assigned to view materials.');
        }

        $materials = Material::where('level_id', $placementTest->final_level_id)
                             ->latest()
                             ->get();

        return view('mentee.materials.index', compact('materials', 'placementTest'));
    }
}
