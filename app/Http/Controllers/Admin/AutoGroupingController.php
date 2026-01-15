<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MentoringGroup;
use App\Models\GroupMember;
use App\Services\AutoGroupingService;
use Illuminate\Support\Facades\DB;

class AutoGroupingController extends Controller
{
    protected $autoGroupingService;

    public function __construct(AutoGroupingService $autoGroupingService)
    {
        $this->autoGroupingService = $autoGroupingService;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Count mentees who are not in any group yet
        $unassignedMenteesCount = User::where('role_id', 3) // 3 is Mentee
            ->whereDoesntHave('groupMember')
            ->count();

        // Count mentors who are not assigned to any group yet
        $availableMentorsCount = User::where('role_id', 2) // 2 is Mentor
            ->whereDoesntHave('mentoringGroupsAsMentor')
            ->count();
            
        $menteesPerGroupDefault = 14; // Default value, can be made configurable

        $estimatedGroups = $unassignedMenteesCount > 0 ? ceil($unassignedMenteesCount / $menteesPerGroupDefault) : 0;

        return view('admin.mentoring-groups.auto-create', compact(
            'unassignedMenteesCount',
            'availableMentorsCount',
            'estimatedGroups',
            'menteesPerGroupDefault'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mentees_per_group' => ['required', 'integer', 'min:1'],
            'delete_all_existing' => ['boolean'],
        ]);

        $menteesPerGroup = $request->input('mentees_per_group');
        $deleteAllExisting = $request->boolean('delete_all_existing');

        try {
            $results = $this->autoGroupingService->handle($menteesPerGroup, $deleteAllExisting);

            $groupsCreatedCount = $results['groups_created'];
            $menteesAssignedCount = $results['mentees_assigned'];
            $mentorsAssignedCount = $results['mentors_assigned'];
            $mentorsExhausted = $results['mentors_exhausted'];
            $totalUnassignedMenteesInitial = $results['total_unassigned_mentees_initial'];

            $message = "Berhasil membuat {$groupsCreatedCount} kelompok baru. {$menteesAssignedCount} mentee telah ditugaskan ke dalam kelompok dengan {$mentorsAssignedCount} mentor.";
            
            $unassignedRemaining = $totalUnassignedMenteesInitial - $menteesAssignedCount;
            $successMessage = 'Proses pengelompokan otomatis telah selesai.';

            if ($mentorsExhausted && $unassignedRemaining > 0) {
                $warningMessage = "Proses berhenti karena mentor yang tersedia telah habis dan {$unassignedRemaining} mentee belum mendapatkan kelompok.";
            } else if ($unassignedRemaining > 0) {
                 $warningMessage = "Terdapat {$unassignedRemaining} mentee yang belum mendapatkan kelompok.";
            }

            $redirect = redirect()->route('admin.mentoring-groups.auto-grouping.create')
                ->with('success', $successMessage)
                ->with('grouping_results', [
                    'groups_created' => $groupsCreatedCount,
                    'mentees_assigned' => $menteesAssignedCount,
                    'unassigned_remaining' => $unassignedRemaining,
                    'warning' => $warningMessage ?? null,
                ]);

            return $redirect;

        } catch (\Exception $e) {
            return redirect()->route('admin.mentoring-groups.auto-grouping.create')->with('danger', 'Terjadi kesalahan saat membuat kelompok: ' . $e->getMessage());
        }
    }
}
