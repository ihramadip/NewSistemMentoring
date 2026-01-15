<?php

namespace App\Http\Controllers;

use App\Models\PlacementTest;
use App\Models\PlacementTestDefinition;
use App\Http\Requests\StorePlacementTestRequest;
use App\Services\PlacementTestService;
use Illuminate\Support\Facades\Auth;

class PlacementTestSubmissionController extends Controller
{
    /**
     * Display the placement test form.
     */
    public function create()
    {
        $user = Auth::user();

        // Check if the user already has a placement test record
        if (PlacementTest::where('mentee_id', $user->id)->exists()) {
            return view('placement-test.completed'); // Show a "You have already completed the test" page
        }

        // Fetch the questions from the database.
        // Eager load the options to prevent N+1 query problems in the view.
        $testDefinition = PlacementTestDefinition::with('questions.options')->first();
        
        // Handle case where no questions are seeded
        if (!$testDefinition) {
            // Or return a view with an appropriate error message
            abort(500, "Placement test has not been configured by an administrator.");
        }

        return view('placement-test.create', ['questions' => $testDefinition->questions]);
    }

    /**
     * Store the placement test submission.
     */
    public function store(StorePlacementTestRequest $request, PlacementTestService $placementTestService)
    {
        $user = Auth::user();

        // Prevent re-submission, an extra layer of protection
        if (PlacementTest::where('mentee_id', $user->id)->exists()) {
            return redirect()->route('dashboard')->with('error', 'You have already submitted your placement test.');
        }

        try {
            $placementTestService->handleSubmission(
                $user,
                $request->validated('answers'),
                $request->file('audio_recording')
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        
        return redirect()->route('dashboard')->with('success', 'Your placement test has been submitted successfully! Please wait for the results.');
    }
}
