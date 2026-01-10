<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::with(['level', 'creator'])->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::all();
        return view('admin.exams.create', compact('levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_id' => 'nullable|exists:levels,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'published_at' => 'nullable|date',
        ]);

        $exam = Exam::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'level_id' => $validatedData['level_id'],
            'duration_minutes' => $validatedData['duration_minutes'],
            'published_at' => $validatedData['published_at'],
            'created_by' => auth()->id(), // Assign current user as creator
        ]);

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil ditambahkan.');
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
    public function edit(Exam $exam)
    {
        $exam->load(['questions.options']); // Eager load questions and their options
        $levels = Level::all();
        return view('admin.exams.edit', compact('exam', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_id' => 'nullable|exists:levels,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'published_at' => 'nullable|date',
        ]);

        $exam->update($validatedData);

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil dihapus.');
    }
}
