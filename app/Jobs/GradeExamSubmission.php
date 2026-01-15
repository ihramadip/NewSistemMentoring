<?php

namespace App\Jobs;

use App\Models\ExamSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GradeExamSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $submission;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\ExamSubmission $submission
     * @return void
     */
    public function __construct(ExamSubmission $submission)
    {
        $this->submission = $submission->load(['exam.questions.options', 'answers']); // Eager load relationships
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $totalScore = 0;

        DB::transaction(function () use (&$totalScore) {
            foreach ($this->submission->answers as $submissionAnswer) {
                $question = $submissionAnswer->question; // Already eager loaded via submission->exam->questions
                $score = 0;

                if ($question) {
                    if ($question->question_type === 'multiple_choice' && $submissionAnswer->chosen_option_id) {
                        $chosenOption = $question->options->find($submissionAnswer->chosen_option_id);
                        if ($chosenOption && $chosenOption->is_correct) {
                            $score = $question->score_value;
                        }
                    }
                    // For essay/audio_response, score remains 0, to be graded manually
                }
                $submissionAnswer->score = $score; // Update score on the submission answer
                $submissionAnswer->save();
                $totalScore += $score;
            }

            $this->submission->total_score = $totalScore;
            $this->submission->status = 'graded'; // Mark as graded
            $this->submission->save();
        });
    }
}