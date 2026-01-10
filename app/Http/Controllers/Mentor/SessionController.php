<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\ProgressReport;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = Session::whereHas('mentoringGroup', function($query) {
                $query->where('mentor_id', auth()->id());
            })
            ->with('mentoringGroup')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('mentor.sessions.index', compact('sessions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session)
    {
        // Ensure the mentor is authorized to see this session
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);
        }

        // Eager load necessary relationships
        $session->load('mentoringGroup.members', 'attendances', 'progressReports');

        // Prepare data for easy access in the view
        $attendances = $session->attendances->keyBy('mentee_id');
        $progressReports = $session->progressReports->keyBy('mentee_id');

        return view('mentor.sessions.show', compact('session', 'attendances', 'progressReports'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
    {
        // Ensure the mentor is authorized to update this session
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', 'in:present,absent,excused'],
            'reports' => ['sometimes', 'array'],
            'reports.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reports.*.reading_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Process Attendances
        foreach ($validated['attendances'] as $menteeId => $data) {
            Attendance::updateOrCreate(
                ['session_id' => $session->id, 'mentee_id' => $menteeId],
                ['status' => $data['status']]
            );
        }

        // Process Progress Reports
        if (isset($validated['reports'])) {
            foreach ($validated['reports'] as $menteeId => $data) {
                // We only create/update a report if there's a score or notes.
                if (!empty($data['score']) || !empty($data['reading_notes'])) {
                    ProgressReport::updateOrCreate(
                        ['session_id' => $session->id, 'mentee_id' => $menteeId],
                        [
                            'score' => $data['score'],
                            'reading_notes' => $data['reading_notes'],
                        ]
                    );
                }
            }
        }

        return redirect()->route('mentor.sessions.show', $session)->with('success', 'Data absensi dan laporan berhasil disimpan.');
    }
}
