<?php

namespace App\Services;

use App\Models\User;
use App\Models\MentoringGroup;
use App\Models\GroupMember;
use App\Models\PlacementTest; // Assuming this is needed for finalLevel
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection; // Explicitly import Collection

class AutoGroupingService
{
    /**
     * Handle the automatic grouping of mentees and mentors.
     *
     * @param int $menteesPerGroup The desired number of mentees per group.
     * @param bool $deleteAllExisting Whether to delete all existing groups before creating new ones.
     * @return array An array containing the number of groups created and mentees assigned.
     * @throws \Exception
     */
    public function handle(int $menteesPerGroup, bool $deleteAllExisting): array
    {
        DB::beginTransaction();
        try {
            if ($deleteAllExisting) {
                $this->clearExistingGroups();
            }

            $availableMentors = $this->getAvailableMentors();
            $unassignedMentees = $this->getUnassignedMentees();

            if ($availableMentors->isEmpty()) {
                throw new \Exception('Tidak ada mentor yang tersedia untuk ditugaskan.');
            }
            if ($unassignedMentees->isEmpty()) {
                throw new \Exception('Tidak ada mentee yang perlu dikelompokkan.');
            }

            $mentorsByFaculty = $availableMentors->groupBy('faculty_id');
            $generalMentorPool = $availableMentors->shuffle();
            $groupedMentees = $this->groupMenteesByCriteria($unassignedMentees);

            $groupsCreatedCount = 0;
            $menteesAssignedCount = 0;
            $totalUnassignedMenteesInitial = $unassignedMentees->count();

            foreach ($groupedMentees as $key => $menteesInGroup) {
                list($facultyId, $levelId, $gender) = explode('_', $key);
                $chunks = $menteesInGroup->chunk($menteesPerGroup);

                foreach ($chunks as $chunk) {
                    $mentor = $this->assignMentor($facultyId, $mentorsByFaculty, $generalMentorPool);
                    
                    if (!$mentor) {
                        Log::warning('AutoGroupingService: No mentor found for a mentee chunk. Stopping process.');
                        break 2;
                    }

                    $this->createMentoringGroup($mentor, $chunk, $groupsCreatedCount, $levelId, $facultyId, $gender);
                    $groupsCreatedCount++;
                    $menteesAssignedCount += $chunk->count();
                }
            }

            DB::commit();

            return [
                'groups_created' => $groupsCreatedCount,
                'mentees_assigned' => $menteesAssignedCount,
                'mentors_assigned' => $groupsCreatedCount,
                'mentors_exhausted' => $generalMentorPool->isEmpty() && $menteesAssignedCount < $totalUnassignedMenteesInitial,
                'total_unassigned_mentees_initial' => $totalUnassignedMenteesInitial,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Auto grouping failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \Exception('Terjadi kesalahan saat membuat kelompok: ' . $e->getMessage());
        }
    }

    /**
     * Clear all existing groups and group members.
     */
    private function clearExistingGroups(): void
    {
        GroupMember::query()->delete();
        MentoringGroup::query()->delete();
    }

    /**
     * Get available mentors (role_id 2) who are not currently assigned to a group.
     *
     * @return Collection
     */
    private function getAvailableMentors(): Collection
    {
        $assignedMentorIds = MentoringGroup::pluck('mentor_id')->unique();
        return User::where('role_id', 2) // Mentor
            ->whereNotIn('id', $assignedMentorIds)
            ->get();
    }

    /**
     * Get unassigned mentees (role_id 3) with their faculty and final level from placement test.
     *
     * @return Collection
     */
    private function getUnassignedMentees(): Collection
    {
        $assignedMenteeIds = GroupMember::pluck('mentee_id')->unique();
        return User::where('role_id', 3) // Mentee
            ->whereNotIn('id', $assignedMenteeIds)
            ->with(['faculty', 'placementTest.finalLevel'])
            ->get();
    }

    /**
     * Group mentees by a composite key: faculty, level, and gender.
     *
     * @param Collection $mentees
     * @return Collection
     */
    private function groupMenteesByCriteria(Collection $mentees): Collection
    {
        return $mentees->groupBy(function ($mentee) {
            $facultyId = $mentee->faculty_id ?? 'unknown';
            // Ensure placementTest and finalLevel exist before accessing properties
            $levelId = $mentee->placementTest->finalLevel->id ?? 'unknown';
            $gender = $mentee->gender ?? 'unknown';
            return "{$facultyId}_{$levelId}_{$gender}";
        });
    }

    /**
     * Assign a mentor from faculty-specific pools or general pool.
     *
     * @param string $facultyId
     * @param Collection $mentorsByFaculty
     * @param Collection $generalMentorPool
     * @return User|null
     */
    private function assignMentor(string $facultyId, Collection &$mentorsByFaculty, Collection &$generalMentorPool): ?User
    {
        $mentor = null;
        
        // Try to get a mentor from the same faculty
        if (isset($mentorsByFaculty[$facultyId]) && $mentorsByFaculty[$facultyId]->isNotEmpty()) {
            $mentor = $mentorsByFaculty[$facultyId]->shift();
            // Also remove this mentor from the general pool to avoid double assignment
            $generalMentorPool = $generalMentorPool->reject(fn($m) => $m->id === $mentor->id);
        } 
        // If no mentor in the same faculty, get from the general pool
        elseif ($generalMentorPool->isNotEmpty()) {
            $mentor = $generalMentorPool->shift();
            // Also remove this mentor from his original faculty group if he exists there
            foreach ($mentorsByFaculty as $fId => $mentors) {
                $mentorsByFaculty[$fId] = $mentors->reject(fn($m) => $m->id === $mentor->id);
            }
        }
        
        return $mentor;
    }

    /**
     * Create a new mentoring group and assign mentees to it.
     *
     * @param User $mentor
     * @param Collection $menteesChunk
     * @param int $groupsCreatedCount
     * @param string $levelId
     * @param string $facultyId
     * @param string $gender
     */
    private function createMentoringGroup(User $mentor, Collection $menteesChunk, int $groupsCreatedCount, string $levelId, string $facultyId, string $gender): void
    {
        $firstMentee = $menteesChunk->first();
        $facultyName = $firstMentee->faculty->name ?? 'Unknown Faculty';
        $levelName = $firstMentee->placementTest->finalLevel->name ?? 'Unknown Level'; 
        $genderName = ($gender === 'male' || $gender === '1') ? 'Ikhwan' : 'Akhwat';
        
        $group = MentoringGroup::create([
            'mentor_id' => $mentor->id,
            'name' => "{$levelName} - {$facultyName} - {$genderName} - " . ($groupsCreatedCount + 1),
            'level_id' => $levelId,
        ]);

        $groupMembersData = [];
        foreach ($menteesChunk as $mentee) {
            $groupMembersData[] = [
                'mentee_id' => $mentee->id,
                'mentoring_group_id' => $group->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        GroupMember::insert($groupMembersData);
    }
}