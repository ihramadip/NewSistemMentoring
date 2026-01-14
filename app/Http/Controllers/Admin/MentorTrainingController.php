<?php

namespace App\Http\Controllers\Admin; // Changed namespace

use App\Http\Controllers\Controller;
use App\Models\MentorTraining;
use Illuminate\Http\Request;

class MentorTrainingController extends Controller // Changed class name
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainings = MentorTraining::orderBy('schedule_date', 'desc')->get();
        return view('mentor.trainings.index', compact('trainings')); // Changed view path for mentor viewing
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mentor-trainings.create'); // Changed view path
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:TFM,Diklat',
            'description' => 'nullable|string',
            'schedule_date' => 'required|date',
            'schedule_time' => 'nullable|string|max:255',
            'material_link' => 'nullable|url',
            'test_link' => 'nullable|url',
        ]);

        MentorTraining::create($validated);

        return redirect()->route('admin.mentor-trainings.index')->with('success', 'Pelatihan mentor berhasil ditambahkan.'); // Changed route name
    }

    /**
     * Display the specified resource.
     */
    public function show(MentorTraining $training)
    {
        // Not typically needed for this CRUD setup, redirect to index
        return redirect()->route('admin.mentor-trainings.index'); // Changed route name
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MentorTraining $training)
    {
        return view('admin.mentor-trainings.edit', compact('training')); // Changed view path
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MentorTraining $training)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:TFM,Diklat',
            'description' => 'nullable|string',
            'schedule_date' => 'required|date',
            'schedule_time' => 'nullable|string|max:255',
            'material_link' => 'nullable|url',
            'test_link' => 'nullable|url',
        ]);

        $training->update($validated);

        return redirect()->route('admin.mentor-trainings.index')->with('success', 'Pelatihan mentor berhasil diperbarui.'); // Changed route name
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MentorTraining $training)
    {
        $training->delete();
        return redirect()->route('admin.mentor-trainings.index')->with('success', 'Pelatihan mentor berhasil dihapus.'); // Changed route name
    }
}
