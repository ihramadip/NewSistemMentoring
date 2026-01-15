<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExamRequest;
use App\Http\Requests\Admin\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Level;
use App\Services\ExamService;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

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
    public function store(StoreExamRequest $request)
    {
        $exam = $this->examService->createExam($request->validated());

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['questions.options']); // Eager load questions and their options
        return view('admin.exams.show', compact('exam'));
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
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $this->examService->updateExam($exam, $request->validated());

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $this->examService->deleteExam($exam);

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Ujian "' . $exam->name . '" berhasil dihapus.');
    }
}
