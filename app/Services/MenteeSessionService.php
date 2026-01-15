<?php

namespace App\Services;

use App\Models\User;
use App\Models\Session;
use App\Models\AdditionalSession;

class MenteeSessionService
{
    /**
     * Get all required and additional sessions for a given mentee's group.
     *
     * @param User $mentee
     * @return array
     */
    public function getSessions(User $mentee): array
    {
        $mentoringGroup = $mentee->mentoringGroupsAsMentee()->first();

        if (!$mentoringGroup) {
            return [
                'sessions' => collect(),
                'additionalSessions' => collect(),
                'mentoringGroup' => null,
            ];
        }

        // Get all mandatory sessions for this mentee's group
        $sessions = Session::where('mentoring_group_id', $mentoringGroup->id)
            ->with(['attendances' => function ($query) use ($mentee) {
                $query->where('mentee_id', $mentee->id);
            }, 'progressReports' => function ($query) use ($mentee) {
                $query->where('mentee_id', $mentee->id);
            }])
            ->orderBy('date', 'asc')
            ->get();

        // Get all additional sessions for this mentee (reverted to original logic)
        $additionalSessions = AdditionalSession::where('mentee_id', $mentee->id)
            ->orderBy('date', 'asc')
            ->get();

        return [
            'sessions' => $sessions,
            'additionalSessions' => $additionalSessions,
            'mentoringGroup' => $mentoringGroup,
        ];
    }
}
