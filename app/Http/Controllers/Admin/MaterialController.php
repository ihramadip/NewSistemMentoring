<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('level')->latest()->get();
        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        $levels = Level::all();
        return view('admin.materials.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_id' => ['required', 'exists:levels,id'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'], // Max 10MB
        ]);

        $filePath = $request->file('file')->store('public/materials');

        Material::create([
            'title' => $request->title,
            'description' => $request->description,
            'level_id' => $request->level_id,
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil diunggah.');
    }

    public function edit(Material $material)
    {
        $levels = Level::all();
        return view('admin.materials.edit', compact('material', 'levels'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_id' => ['required', 'exists:levels,id'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'], // Max 10MB
        ]);

        $filePath = $material->file_path;
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::delete($material->file_path);
            }
            // Store new file
            $filePath = $request->file('file')->store('public/materials');
        }

        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'level_id' => $request->level_id,
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        // Delete the file from storage
        if ($material->file_path) {
            Storage::delete($material->file_path);
        }
        
        $material->delete();

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}
