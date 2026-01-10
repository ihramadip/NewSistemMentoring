<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->with('options')->paginate(10);
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Exam $exam)
    {
        // Define question types for the form dropdown
        $questionTypes = [
            'multiple_choice' => 'Pilihan Ganda',
            'essay' => 'Esai',
            'audio_response' => 'Respon Audio',
        ];
        return view('admin.questions.create', compact('exam', 'questionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Exam $exam)
    {
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay,audio_response',
            'score_value' => 'required|integer|min:1',
            'options' => 'array', // For multiple choice
            'options.*.text' => 'required_with:options|string|max:255',
            'options.*.is_correct' => 'boolean',
        ]);

        // If multiple_choice, ensure at least one option is present and one is correct
        if ($validatedData['question_type'] === 'multiple_choice') {
            if (empty($validatedData['options'])) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi.'])->withInput();
            }
            $hasCorrectOption = collect($validatedData['options'])->pluck('is_correct')->filter()->isNotEmpty();
            if (!$hasCorrectOption) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi yang benar.'])->withInput();
            }
        }

        $question = $exam->questions()->create([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
            'score_value' => $validatedData['score_value'],
        ]);

        if ($validatedData['question_type'] === 'multiple_choice' && isset($validatedData['options'])) {
            foreach ($validatedData['options'] as $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'] ?? false,
                ]);
            }
        }

        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil ditambahkan ke ujian ' . $exam->name . '.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam, Question $question)
    {
        $question->load('options'); // Eager load options for editing
        $questionTypes = [
            'multiple_choice' => 'Pilihan Ganda',
            'essay' => 'Esai',
            'audio_response' => 'Respon Audio',
        ];
        return view('admin.questions.edit', compact('exam', 'question', 'questionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam, Question $question)
    {
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay,audio_response',
            'score_value' => 'required|integer|min:1',
            'options' => 'array', // For multiple choice
            'options.*.text' => 'required_with:options|string|max:255',
            'options.*.is_correct' => 'boolean',
        ]);

        // If multiple_choice, ensure at least one option is present and one is correct
        if ($validatedData['question_type'] === 'multiple_choice') {
            if (empty($validatedData['options'])) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi.'])->withInput();
            }
            $hasCorrectOption = collect($validatedData['options'])->pluck('is_correct')->filter()->isNotEmpty();
            if (!$hasCorrectOption) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi yang benar.'])->withInput();
            }
        }

        $question->update([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
            'score_value' => $validatedData['score_value'],
        ]);

        // Handle options update
        if ($validatedData['question_type'] === 'multiple_choice' && isset($validatedData['options'])) {
            // Delete old options that are no longer present
            $existingOptionIds = collect($validatedData['options'])->filter(fn($opt) => isset($opt['id']))->pluck('id');
            $question->options()->whereNotIn('id', $existingOptionIds)->delete();

            foreach ($validatedData['options'] as $optionData) {
                if (isset($optionData['id'])) {
                    // Update existing option
                    $option = $question->options()->find($optionData['id']);
                    if ($option) {
                        $option->update([
                            'option_text' => $optionData['text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                        ]);
                    }
                } else {
                    // Create new option
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                }
            }
        } else {
            // If question type is no longer multiple_choice, delete all its options
            $question->options()->delete();
        }

        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, Question $question)
    {
        $question->delete();

        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil dihapus dari ujian ' . $exam->name . '.');
    }
}
