<?php

namespace App\Services\Statistic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndividualStatisticService
{
    /**
     * Get paginated individual mentee analysis with search capability.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedAnalysis(Request $request)
    {
        $query = DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->where('users.role_id', 3) // Mentee
            ->where('exam_submissions.status', 'graded')
            ->whereNotNull('placement_tests.audio_reading_score')
            ->whereNotNull('placement_tests.theory_score')
            ->select(
                'users.npm',
                'users.name',
                DB::raw('ROUND((placement_tests.audio_reading_score + placement_tests.theory_score) / 2, 2) as placement_score'),
                'exam_submissions.total_score as final_exam_score'
            );

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('users.name', 'like', $searchTerm)
                  ->orWhere('users.npm', 'like', $searchTerm);
            });
        }
        
        return $query->orderBy('users.npm')->paginate(10)->withQueryString();
    }
}
