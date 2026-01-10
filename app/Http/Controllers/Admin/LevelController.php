<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $levels = Level::all();
        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.levels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels'],
            'description' => ['nullable', 'string'],
        ]);

        Level::create($request->all());

        return redirect()->route('admin.levels.index')->with('success', 'Level berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        return redirect()->route('admin.levels.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('levels')->ignore($level->id)],
            'description' => ['nullable', 'string'],
        ]);

        $level->update($request->all());

        return redirect()->route('admin.levels.index')->with('success', 'Level berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        $level->delete();
        return redirect()->route('admin.levels.index')->with('success', 'Level berhasil dihapus.');
    }
}
