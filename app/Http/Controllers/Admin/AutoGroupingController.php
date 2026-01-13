<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MentoringGroup;
use App\Models\GroupMember;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\ProgressReport;
use App\Models\SubmissionAnswer;
use App\Models\ExamSubmission;
use App\Models\PlacementTest;
use Illuminate\Support\Facades\DB;

class AutoGroupingController extends Controller
{
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
            
        $menteesPerGroup = 14;
        $estimatedGroups = $unassignedMenteesCount > 0 ? ceil($unassignedMenteesCount / $menteesPerGroup) : 0;

        return view('admin.mentoring-groups.auto-create', compact(
            'unassignedMenteesCount',
            'availableMentorsCount',
            'estimatedGroups'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Ensure clean start: Delete existing groups and group members
            GroupMember::query()->delete();
            MentoringGroup::query()->delete();

            // 1. Get available mentors more efficiently
            $assignedMentorIds = MentoringGroup::pluck('mentor_id')->unique();
            $availableMentors = User::where('role_id', 2) // Mentor
                ->whereNotIn('id', $assignedMentorIds)
                ->get();

            // 2. Get unassigned mentees more efficiently
            $assignedMenteeIds = GroupMember::pluck('mentee_id')->unique();
            $unassignedMentees = User::where('role_id', 3) // Mentee
                ->whereNotIn('id', $assignedMenteeIds)
                ->with(['faculty', 'placementTest.finalLevel'])
                ->get();

            // dd($unassignedMentees->count(), $availableMentors->count());

            if ($availableMentors->isEmpty()) {
                return redirect()->route('admin.mentoring-groups.auto-grouping.create')->with('warning', 'Tidak ada mentor yang tersedia untuk ditugaskan.');
            }
            if ($unassignedMentees->isEmpty()) {
                return redirect()->route('admin.mentoring-groups.auto-grouping.create')->with('warning', 'Tidak ada mentee yang perlu dikelompokkan.');
            }

            // 2. Group mentors by faculty for prioritized assignment
            $mentorsByFaculty = $availableMentors->groupBy('faculty_id');
            // Create a general pool of mentors for when a faculty runs out
            $generalMentorPool = $availableMentors->shuffle();


            // 3. Group mentees by a composite key: faculty, level, and gender
            $groupedMentees = $unassignedMentees->groupBy(function ($mentee) {
                $facultyId = $mentee->faculty_id ?? 'unknown';
                $levelId = $mentee->placementTest->final_level_id ?? 'unknown';
                $gender = $mentee->gender ?? 'unknown';
                return "{$facultyId}_{$levelId}_{$gender}";
            });

            $menteesPerGroup = 14;
            $groupsCreatedCount = 0;
            $menteesAssignedCount = 0;
            
            // 4. Iterate through each group and create mentoring groups
            foreach ($groupedMentees as $key => $menteesInGroup) {
                // Extract details from the group key
                list($facultyId, $levelId, $gender) = explode('_', $key);

                $chunks = $menteesInGroup->chunk($menteesPerGroup);

                foreach ($chunks as $chunk) {
                    // --- New Mentor Assignment Logic ---
                    $mentor = null;
                    
                    // a. Try to get a mentor from the same faculty
                    if (isset($mentorsByFaculty[$facultyId]) && $mentorsByFaculty[$facultyId]->isNotEmpty()) {
                        $mentor = $mentorsByFaculty[$facultyId]->shift();
                        // Also remove this mentor from the general pool to avoid double assignment
                        $generalMentorPool = $generalMentorPool->reject(fn($m) => $m->id === $mentor->id);
                    } 
                    // b. If no mentor in the same faculty, get from the general pool
                    elseif ($generalMentorPool->isNotEmpty()) {
                        $mentor = $generalMentorPool->shift();
                        // Also remove this mentor from his original faculty group if he exists there
                        if(isset($mentorsByFaculty[$mentor->faculty_id])) {
                           $mentorsByFaculty[$mentor->faculty_id] = $mentorsByFaculty[$mentor->faculty_id]->reject(fn($m) => $m->id === $mentor->id);
                        }
                    } 
                    // c. If no mentors are available at all, stop the process
                    else {
                        break 2; // Break out of both foreach loops
                    }
                    // --- End of New Logic ---
                    
                    $firstMentee = $chunk->first();
                    $facultyName = $firstMentee->faculty->name ?? 'Unknown Faculty';
                    $levelName = $firstMentee->placementTest->finalLevel->name ?? 'Unknown Level';
                    $genderName = $gender === 'male' ? 'Ikhwan' : 'Akhwat';
                    
                    $group = MentoringGroup::create([
                        'mentor_id' => $mentor->id,
                        'name' => "{$levelName} - {$facultyName} - {$genderName} - " . ($groupsCreatedCount + 1),
                        'level_id' => $levelId,
                    ]);
                    $groupsCreatedCount++;

                    // 6. Assign mentees to the new group
                    $groupMembersData = [];
                    foreach ($chunk as $mentee) {
                        $groupMembersData[] = [
                            'mentee_id' => $mentee->id,
                            'mentoring_group_id' => $group->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    GroupMember::insert($groupMembersData);
                    $menteesAssignedCount += count($groupMembersData);
                }
            }

            DB::commit();

            $mentorsAssignedCount = $groupsCreatedCount;
            $message = "Berhasil membuat {$groupsCreatedCount} kelompok baru. {$menteesAssignedCount} mentee telah ditugaskan ke dalam kelompok dengan {$mentorsAssignedCount} mentor.";
            
            if ($generalMentorPool->isEmpty() && $menteesAssignedCount < $unassignedMentees->count()) {
                $message .= " Proses berhenti karena mentor yang tersedia telah habis.";
            }

            return redirect()->route('admin.mentoring-groups.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.mentoring-groups.auto-grouping.create')->with('danger', 'Terjadi kesalahan saat membuat kelompok: ' . $e->getMessage());
        }
    }
}
