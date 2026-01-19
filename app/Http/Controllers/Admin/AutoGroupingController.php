<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MentoringGroup;
use App\Models\GroupMember;
use App\Services\AutoGroupingService;
use Illuminate\Support\Facades\DB;

/**
 * AutoGroupingController
 *
 * Controller untuk automatic grouping mentee (MODULE B #4: Grouping)
 * Distribute unassigned mentee ke mentoring groups dengan balanced assignment
 *
 * Fitur:
 * - Show form dengan summary: unassigned mentees, available mentors, estimated groups
 * - Admin dapat set mentees per group & choose delete existing groups
 * - Call AutoGroupingService untuk handle grouping logic
 * - Service assign mentees ke mentors based on availability & capacity
 * - Handle case: mentors exhausted, some mentees unassigned
 * - Return detail results: groups created, mentees assigned, mentors assigned, warnings
 *
 * Flow:
 * 1. Admin akses auto-grouping.create (show form)
 * 2. Form shows: count unassigned mentees, count available mentors, estimated groups
 * 3. Admin input: mentees_per_group (e.g., 14), option delete_all_existing
 * 4. Admin submit form (POST auto-grouping.store)
 * 5. Service process: delete existing (if selected), assign mentees, assign mentors
 * 6. Service return: groups_created, mentees_assigned, mentors_assigned, mentors_exhausted
 * 7. Redirect dengan detail results & warning jika mentors exhausted/mentees unassigned
 *
 * @package App\Http\Controllers\Admin
 */
class AutoGroupingController extends Controller
{
    /**
     * Service untuk handle automatic grouping logic
     * Di-inject via constructor DI
     *
     * @var AutoGroupingService
     */
    protected $autoGroupingService;

    /**
     * Constructor - Inject AutoGroupingService
     *
     * @param AutoGroupingService $autoGroupingService Service untuk grouping logic
     */
    public function __construct(AutoGroupingService $autoGroupingService)
    {
        $this->autoGroupingService = $autoGroupingService;
    }

    /**
     * Menampilkan form untuk automatic grouping
     *
     * Proses:
     * 1. Count unassigned mentees (role_id = 3, tidak ada di groupMember)
     * 2. Count available mentors (role_id = 2, tidak memiliki mentoringGroup)
     * 3. Calculate estimated groups berdasarkan default 14 mentees per group
     * 4. Return view dengan summary data
     *
     * Query optimizations:
     * - whereDoesntHave('groupMember') untuk mentees tanpa group
     * - whereDoesntHave('mentoringGroupsAsMentor') untuk mentors tanpa group
     *
     * @return \Illuminate\View\View View form auto-grouping dengan summary
     */
    public function create()
    {
        // Count mentees yang belum ter-assign ke group manapun
        // Query: role_id = 3 (Mentee) AND tidak punya record di GroupMember
        $unassignedMenteesCount = User::where('role_id', 3)
            ->whereDoesntHave('groupMember')
            ->count();

        // Count mentors yang belum ter-assign ke group manapun
        // Query: role_id = 2 (Mentor) AND tidak punya MentoringGroup
        $availableMentorsCount = User::where('role_id', 2)
            ->whereDoesntHave('mentoringGroupsAsMentor')
            ->count();

        // Default mentees per group (bisa dikonfigurasi dari admin input)
        $menteesPerGroupDefault = 14;

        // Hitung estimasi jumlah groups yang akan dibuat
        // Formula: ceil(unassignedMentees / menteesPerGroup)
        $estimatedGroups = $unassignedMenteesCount > 0 ? ceil($unassignedMenteesCount / $menteesPerGroupDefault) : 0;

        // Return view dengan data untuk ditampilkan di form
        return view('admin.mentoring-groups.auto-create', compact(
            'unassignedMenteesCount',
            'availableMentorsCount',
            'estimatedGroups',
            'menteesPerGroupDefault'
        ));
    }

    /**
     * Menyimpan hasil automatic grouping
     *
     * Proses:
     * 1. Validasi input: mentees_per_group (required, integer, min 1), delete_all_existing (boolean)
     * 2. Extract nilai dari request
     * 3. Call service->handle() dengan parameter & receive results array
     * 4. Service return: groups_created, mentees_assigned, mentors_assigned, mentors_exhausted, total_unassigned_mentees_initial
     * 5. Calculate unassignedRemaining = total_initial - menteesAssigned
     * 6. Build warning messages jika mentors exhausted atau mentees unassigned
     * 7. Redirect ke create view dengan success message & grouping_results detail
     * 8. Error: catch exception & redirect dengan danger message
     *
     * Hasil yang di-session:
     * - success: pesan sukses
     * - grouping_results: array dengan groups_created, mentees_assigned, unassigned_remaining, warning
     * - danger: pesan error jika exception
     *
     * Warning conditions:
     * - mentorsExhausted && unassignedRemaining > 0: Mentors habis, mentees masih banyak
     * - unassignedRemaining > 0 (tapi mentors tidak exhausted): Beberapa mentees unassigned
     *
     * @param Request $request Form request dengan mentees_per_group & delete_all_existing
     * @return \Illuminate\Http\RedirectResponse Redirect ke create view dengan results
     */
    public function store(Request $request)
    {
        // Validasi form input
        $request->validate([
            'mentees_per_group' => ['required', 'integer', 'min:1'],
            'delete_all_existing' => ['boolean'],
        ]);

        // Extract nilai dari request
        $menteesPerGroup = $request->input('mentees_per_group');
        $deleteAllExisting = $request->boolean('delete_all_existing');

        try {
            // Call service untuk handle grouping logic
            // Service return: groups_created, mentees_assigned, mentors_assigned, mentors_exhausted, total_unassigned_mentees_initial
            $results = $this->autoGroupingService->handle($menteesPerGroup, $deleteAllExisting);

            // Extract hasil dari service
            $groupsCreatedCount = $results['groups_created'];
            $menteesAssignedCount = $results['mentees_assigned'];
            $mentorsAssignedCount = $results['mentors_assigned'];
            $mentorsExhausted = $results['mentors_exhausted'];
            $totalUnassignedMenteesInitial = $results['total_unassigned_mentees_initial'];

            // Calculate jumlah mentees yang masih unassigned setelah service
            $unassignedRemaining = $totalUnassignedMenteesInitial - $menteesAssignedCount;

            // Success message
            $successMessage = 'Proses pengelompokan otomatis telah selesai.';

            // Build warning messages based on grouping results
            if ($mentorsExhausted && $unassignedRemaining > 0) {
                // Case: Mentors habis tapi masih ada mentees unassigned
                $warningMessage = "Proses berhenti karena mentor yang tersedia telah habis dan {$unassignedRemaining} mentee belum mendapatkan kelompok.";
            } else if ($unassignedRemaining > 0) {
                // Case: Beberapa mentees unassigned (tapi mentors tidak exhausted)
                $warningMessage = "Terdapat {$unassignedRemaining} mentee yang belum mendapatkan kelompok.";
            }

            // Redirect ke create view dengan success message & grouping details
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
            // Error handling: catch exception & redirect dengan error message
            return redirect()->route('admin.mentoring-groups.auto-grouping.create')
                            ->with('danger', 'Terjadi kesalahan saat membuat kelompok: ' . $e->getMessage());
        }
    }
}
