<?php

namespace App\Http\Controllers;

use App\Models\AdditionalSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdditionalSessionController extends Controller
{
    private const MAX_SESSIONS = 21;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // This will be handled by MenteeSessionController
        return redirect()->route('mentee.sessions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mentee = Auth::user();
        $sessionsCount = AdditionalSession::where('mentee_id', $mentee->id)->count();

        if ($sessionsCount >= self::MAX_SESSIONS) {
            return redirect()->route('mentee.sessions.index')->with('warning', 'Anda sudah mencapai batas maksimal 21 sesi tambahan.');
        }

        return view('mentee.additional-sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mentee = Auth::user();
        $mentoringGroup = $mentee->mentoringGroupsAsMentee()->first();

        $mentoringGroup = $mentee->mentoringGroupsAsMentee()->first();

        if (!$mentoringGroup) {
            return redirect()->route('mentee.sessions.index')->with('error', 'Anda tidak tergabung dalam kelompok mentoring manapun.');
        }
        
        $sessionsCount = AdditionalSession::where('mentee_id', $mentee->id)->count();
        if ($sessionsCount >= self::MAX_SESSIONS) {
            return redirect()->route('mentee.sessions.index')->with('warning', 'Anda sudah mencapai batas maksimal 21 sesi tambahan.');
        }

        $request->validate([
            'topic' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:sudah,belum',
            'proof' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('proof')) {
            $filePath = $request->file('proof')->store('public/proofs');
        }

        AdditionalSession::create([
            'mentee_id' => $mentee->id,
            'mentoring_group_id' => $mentoringGroup->id,
            'topic' => $request->topic,
            'date' => $request->date,
            'status' => $request->status,
            'proof_path' => $filePath,
        ]);

        return redirect()->route('mentee.sessions.index')->with('success', 'Sesi tambahan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AdditionalSession $additionalSession)
    {
        // Not typically needed for this CRUD setup
        return redirect()->route('mentee.sessions.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdditionalSession $additionalSession)
    {
        // Authorization check
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }
        return view('mentee.additional-sessions.edit', compact('additionalSession'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdditionalSession $additionalSession)
    {
        // Authorization check
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'topic' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:sudah,belum',
            'proof' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $filePath = $additionalSession->proof_path;
        if ($request->hasFile('proof')) {
            // Delete old file if it exists
            if ($filePath) {
                Storage::delete($filePath);
            }
            $filePath = $request->file('proof')->store('public/proofs');
        }

        $additionalSession->update([
            'topic' => $request->topic,
            'date' => $request->date,
            'status' => $request->status,
            'proof_path' => $filePath,
        ]);

        return redirect()->route('mentee.sessions.index')->with('success', 'Sesi tambahan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdditionalSession $additionalSession)
    {
        // Authorization check
        if ($additionalSession->mentee_id !== Auth::id()) {
            abort(403);
        }

        // Delete the associated file
        if ($additionalSession->proof_path) {
            Storage::delete($additionalSession->proof_path);
        }

        $additionalSession->delete();

        return redirect()->route('mentee.sessions.index')->with('success', 'Sesi tambahan berhasil dihapus.');
    }
}