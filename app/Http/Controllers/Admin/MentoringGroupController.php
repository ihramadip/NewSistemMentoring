<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MentoringGroup;
use App\Models\User;
use App\Models\Level;

class MentoringGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentoringGroups = MentoringGroup::with(['mentor', 'level'])->paginate(10);
        return view('admin.mentoring-groups.index', compact('mentoringGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::all();
        $mentors = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentor');
        })->get();
        return view('admin.mentoring-groups.create', compact('levels', 'mentors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:mentoring_groups,name',
            'mentor_id' => 'required|exists:users,id',
            'level_id' => 'required|exists:levels,id',
            'schedule_info' => 'nullable|string|max:255',
        ]);

        MentoringGroup::create($validatedData);

        return redirect()->route('admin.mentoring-groups.index')
                         ->with('success', 'Kelompok mentoring berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MentoringGroup $mentoringGroup)
    {
        $mentoringGroup->load(['mentor', 'members.faculty']);

        return view('admin.mentoring-groups.show', compact('mentoringGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MentoringGroup $mentoringGroup)
    {
        $levels = Level::all();
        $mentors = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentor');
        })->get();
        $mentees = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentee');
        })->get();

        return view('admin.mentoring-groups.edit', compact('mentoringGroup', 'levels', 'mentors', 'mentees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MentoringGroup $mentoringGroup)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:mentoring_groups,name,' . $mentoringGroup->id,
            'mentor_id' => 'required|exists:users,id',
            'level_id' => 'required|exists:levels,id',
            'schedule_info' => 'nullable|string|max:255',
            'mentee_ids' => 'nullable|array',
            'mentee_ids.*' => 'exists:users,id',
        ]);

        $mentoringGroup->update([
            'name' => $validatedData['name'],
            'mentor_id' => $validatedData['mentor_id'],
            'level_id' => $validatedData['level_id'],
            'schedule_info' => $validatedData['schedule_info'],
        ]);

        // Sync mentees
        $mentoringGroup->members()->sync($validatedData['mentee_ids'] ?? []);

        return redirect()->route('admin.mentoring-groups.index')
                         ->with('success', 'Kelompok mentoring berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MentoringGroup $mentoringGroup)
    {
        $mentoringGroup->delete();

        return redirect()->route('admin.mentoring-groups.index')
                         ->with('success', 'Kelompok mentoring berhasil dihapus.');
    }
}
