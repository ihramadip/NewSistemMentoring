<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\PlacementTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MenteeExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->role->name === 'Admin'; // Identify if the current user is an Admin
        $availableExams = collect();
        $completedExams = collect();

        if ($isAdmin) {
            // Admins see all published exams. The 'published_at' filter is still relevant for admins
            // as it indicates if an exam is ready to be taken by mentees.
            $availableExams = Exam::with('level')
                                ->whereNotNull('published_at')
                                ->where('published_at', '<=', Carbon::now())
                                ->orderBy('published_at', 'desc')
                                ->paginate(10);
            
            // For admin, show all exams that have at least one submission as "completed" by any mentee.
            // This is a more comprehensive view for an admin.
            $submittedExamIds = ExamSubmission::pluck('exam_id')->unique(); // IDs of exams that have been submitted at least once
            $completedExams = Exam::whereIn('id', $submittedExamIds)->with('level')->get();

        } else { // Is a Mentee
            // Get IDs of exams already submitted by this mentee
            $submittedExamIds = ExamSubmission::where('mentee_id', $user->id)->pluck('exam_id');

            // Show all published exams regardless of level, unless already submitted
            // The user explicitly requested to ignore level filtering for now.
            // We still consider 'published_at' for mentees to ensure they only see active exams.
            $availableExams = Exam::with('level')
                                ->whereNotNull('published_at')
                                ->where('published_at', '<=', Carbon::now())
                                ->whereNotIn('id', $submittedExamIds)
                                ->orderBy('published_at', 'desc')
                                ->paginate(10);
                                
            $completedExams = Exam::whereIn('id', $submittedExamIds)->with('level')->get();
        }

        return view('mentee.exams.index', compact('availableExams', 'completedExams', 'isAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This is not applicable for mentee exams
        abort(404);
    }

    public function store(Request $request, Exam $exam)
    {
        $user = Auth::user();

        // Prevent re-submission
        if (ExamSubmission::where('mentee_id', $user->id)->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('mentee.exams.index')->with('warning', 'Anda sudah pernah mengikuti ujian ini.');
        }

        // Validate answers (basic validation for now, more complex rules can be added)
        $validatedData = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.chosen_option_id' => 'nullable|exists:options,id', // For multiple choice
            'answers.*.answer_text' => 'nullable|string', // For essay/audio response
        ]);

        $exam->load('questions.options'); // Load questions and their options for scoring

        $totalScore = 0;

        // Create ExamSubmission
        $submission = ExamSubmission::create([
            'mentee_id' => $user->id,
            'exam_id' => $exam->id,
            'submitted_at' => Carbon::now(),
            'status' => 'submitted', // Will be updated to 'graded' after admin review
        ]);

        foreach ($validatedData['answers'] as $answerData) {
            $question = $exam->questions->find($answerData['question_id']);
            $score = 0;

            if ($question) {
                if ($question->question_type === 'multiple_choice' && isset($answerData['chosen_option_id'])) {
                    $chosenOption = $question->options->find($answerData['chosen_option_id']);
                    if ($chosenOption && $chosenOption->is_correct) {
                        $score = $question->score_value;
                    }
                } elseif ($question->question_type === 'essay' || $question->question_type === 'audio_response') {
                    // Essay/audio questions will be graded manually by admin, score defaults to 0 for now
                    $score = 0; 
                }
                $totalScore += $score;

                $submission->answers()->create([
                    'question_id' => $question->id,
                    'chosen_option_id' => $answerData['chosen_option_id'] ?? null,
                    'answer_text' => $answerData['answer_text'] ?? null,
                    'score' => $score,
                ]);
            }
        }

        // Update total score for submission (only includes auto-graded MCQs)
        $submission->update(['total_score' => $totalScore]);

        return redirect()->route('mentee.exams.completed')->with('success', 'Ujian Anda telah berhasil dikirimkan. Silakan tunggu hasil penilaian.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $user = Auth::user();

        // Check if mentee has already submitted this exam
        if (ExamSubmission::where('mentee_id', $user->id)->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('mentee.exams.index')->with('warning', 'Anda sudah pernah mengikuti ujian ini.');
        }

        // Check if exam is published
        if (!$exam->published_at || $exam->published_at > Carbon::now()) {
            return redirect()->route('mentee.exams.index')->with('error', 'Ujian ini belum dipublikasikan atau sudah kadaluarsa.');
        }

        // Check mentee's level eligibility if exam has a specific level
        if ($exam->level_id) {
            $placementTest = PlacementTest::where('mentee_id', $user->id)
                                        ->where('final_level_id', $exam->level_id)
                                        ->first();
            if (!$placementTest) {
                return redirect()->route('mentee.exams.index')->with('error', 'Anda tidak memenuhi syarat untuk mengikuti ujian ini berdasarkan level Anda.');
            }
        }

        // Load questions with options for the exam
        $exam->load('questions.options');

        return view('mentee.exams.show', compact('exam'));
    }

    public function completed()
    {
        // Check if the session has the success message to prevent direct access
        if (!session('success')) {
            return redirect()->route('mentee.exams.index');
        }
        return view('mentee.exams.completed');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not applicable for mentee exams
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
