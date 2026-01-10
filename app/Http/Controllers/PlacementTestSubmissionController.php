<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlacementTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlacementTestSubmissionController extends Controller
{
    // A simple, hardcoded question set for the theory test.
    // In a real application, this should come from a database.
    private $theory_questions = [
        1 => ['question' => 'Apa hukum bacaan Nun Sukun bertemu dengan huruf Ba (ب)?', 'options' => ['Idgham', 'Iqlab', 'Ikhfa', 'Izhar'], 'answer' => 'Iqlab'],
        2 => ['question' => 'Berikut ini yang termasuk huruf Qalqalah adalah...', 'options' => ['ق', 'ل', 'م', 'ن'], 'answer' => 'ق'],
        3 => ['question' => 'Membaca Al-Qur\'an dengan tartil artinya...', 'options' => ['Cepat dan lancar', 'Perlahan dan jelas', 'Dengan suara keras', 'Dengan irama'], 'answer' => 'Perlahan dan jelas'],
        4 => ['question' => 'Apa yang dimaksud dengan Mad Wajib Muttasil?', 'options' => ['Mad bertemu hamzah dalam satu kata', 'Mad bertemu hamzah di lain kata', 'Mad bertemu sukun', 'Mad bertemu tasydid'], 'answer' => 'Mad bertemu hamzah dalam satu kata'],
        5 => ['question' => 'Berapa harakat panjang bacaan Mad Jaiz Munfasil?', 'options' => ['2 harakat', '4 atau 5 harakat', '6 harakat', '2, 4, atau 6 harakat'], 'answer' => '4 atau 5 harakat'],
    ];

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

        return view('placement-test.create', ['questions' => $this->theory_questions]);
    }

    /**
     * Store the placement test submission.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Prevent re-submission
        if (PlacementTest::where('mentee_id', $user->id)->exists()) {
            return redirect()->route('dashboard')->with('error', 'You have already submitted your placement test.');
        }

        $request->validate([
            'audio_recording' => ['required', 'file', 'mimes:mp3,wav,m4a,ogg', 'max:10240'], // 10MB Max
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ]);

        // --- Theory Score Calculation ---
        $score = 0;
        $totalQuestions = count($this->theory_questions);
        $userAnswers = $request->input('answers');

        foreach ($this->theory_questions as $id => $questionData) {
            if (isset($userAnswers[$id]) && $userAnswers[$id] === $questionData['answer']) {
                $score++;
            }
        }
        $theory_score = ($score / $totalQuestions) * 100;


        // --- Audio File Handling ---
        $path = $request->file('audio_recording')->store('placement-tests/audio', 'local');


        // --- Create Placement Test Record ---
        try {
            PlacementTest::create([
                'mentee_id' => $user->id,
                'audio_recording_path' => $path,
                'theory_score' => $theory_score,
                'audio_reading_score' => null, // To be graded by admin
                'final_level_id' => null,      // To be assigned by admin
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create placement test record for user ' . $user->id . ': ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
        
        return redirect()->route('dashboard')->with('success', 'Your placement test has been submitted successfully! Please wait for the results.');
    }
}
