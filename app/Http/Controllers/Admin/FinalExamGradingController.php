<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;

class FinalExamGradingController extends Controller
{
    /**
     * Display a listing of exam submissions that need grading.
     */
    public function index(Request $request)
    {
        $query = ExamSubmission::query()
            ->join('users', 'exam_submissions.mentee_id', '=', 'users.id')
            ->select('exam_submissions.*');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.npm', 'like', "%{$search}%");
            });
        }
        
        $submissions = $query->with(['mentee', 'exam'])
            ->orderBy('users.npm', 'asc')
            ->paginate(30)
            ->withQueryString();

        return view('admin.final-exam-grading.index', compact('submissions'));
    }

    /**
     * Show the form for grading the specified exam submission.
     */
    public function edit(ExamSubmission $submission)
    {
        // Eager load relationships for the view
        $submission->load(['mentee', 'exam.questions.options', 'answers.question', 'answers.option']);

        return view('admin.final-exam-grading.edit', compact('submission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSubmission $submission)
    {
        $request->validate([
            'total_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $submission->update([
            'total_score' => $request->total_score,
            'status' => 'graded',
        ]);

        return redirect()->route('admin.final-exam-grading.index')
                         ->with('success', 'Nilai untuk ' . $submission->mentee->name . ' berhasil disimpan.');
    }
}
