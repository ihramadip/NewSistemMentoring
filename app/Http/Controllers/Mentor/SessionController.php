<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\MentoringGroup;
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
     * Show the form for selecting a group to create a session for.
     */
    public function selectGroupForSession()
    {
        $groups = MentoringGroup::where('mentor_id', auth()->id())->get();

        return view('mentor.sessions.select-group', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(MentoringGroup $group)
    {
        // Ensure the mentor is authorized to create a session for this group
        if ($group->mentor_id !== Auth::id()) {
            abort(403);
        }

        return view('mentor.sessions.create', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MentoringGroup $group)
    {
        // Ensure the mentor is authorized to create a session for this group
        if ($group->mentor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Calculate the next session number
        $lastSessionNumber = $group->sessions()->max('session_number') ?? 0;
        $nextSessionNumber = $lastSessionNumber + 1;

        $group->sessions()->create([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'session_number' => $nextSessionNumber,
        ]);

        return redirect()->route('mentor.groups.show', $group)->with('success', 'Sesi baru berhasil dibuat.');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session)
    {
        // Ensure the mentor is authorized to delete this session
        if ($session->mentoringGroup->mentor_id !== Auth::id()) {
            abort(403);
        }

        $session->delete();

        return redirect()->route('mentor.sessions.index')->with('success', 'Sesi berhasil dihapus.');
    }
}
