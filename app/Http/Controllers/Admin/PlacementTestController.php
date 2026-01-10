<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementTest;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlacementTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testResults = PlacementTest::with(['mentee', 'finalLevel'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.placement-tests.index', compact('testResults'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Results are not created this way, but are tied to a mentee submission.
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlacementTest $placementTest)
    {
        // The 'edit' view is more useful for this workflow
        return redirect()->route('admin.placement-tests.edit', $placementTest);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlacementTest $placementTest)
    {
        $levels = Level::all();
        return view('admin.placement-tests.edit', compact('placementTest', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlacementTest $placementTest)
    {
        $request->validate([
            'audio_reading_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'theory_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'final_level_id' => ['nullable', 'exists:levels,id'],
        ]);

        $placementTest->update($request->all());

        return redirect()->route('admin.placement-tests.index')
                         ->with('success', 'Placement test result for ' . $placementTest->mentee->name . ' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlacementTest $placementTest)
    {
        $placementTest->delete();
        
        return redirect()->route('admin.placement-tests.index')
                         ->with('success', 'Placement test result deleted successfully.');
    }

    /**
     * Stream the audio recording for the specified placement test.
     */
    public function streamAudio(PlacementTest $placementTest)
    {
        if (!$placementTest->audio_recording_path || !Storage::disk('local')->exists($placementTest->audio_recording_path)) {
            abort(404, 'Audio file not found.');
        }

        return Storage::disk('local')->response($placementTest->audio_recording_path);
    }
}
