<?php

namespace App\Services;

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

class ExamService
{
    /**
     * Create a new exam.
     *
     * @param array $data The validated data from the request.
     * @return Exam
     */
    public function createExam(array $data): Exam
    {
        return Exam::create($data + ['created_by' => Auth::id()]);
    }

    /**
     * Update an existing exam.
     *
     * @param Exam $exam The exam instance to update.
     * @param array $data The validated data from the request.
     * @return Exam
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        $exam->update($data);
        return $exam;
    }

    /**
     * Delete an exam.
     *
     * @param Exam $exam The exam instance to delete.
     * @return void
     */
    public function deleteExam(Exam $exam): void
    {
        $exam->delete();
    }
}
