<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\User;
use App\Models\Question;
use App\Models\ExamSubmission;
use App\Models\SubmissionAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks for massive insert
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Clean the tables before seeding to avoid duplicates
        ExamSubmission::truncate();
        SubmissionAnswer::truncate();

        // Let's use Exam with ID 1
        $exam = Exam::find(1);
        if (!$exam) {
            $this->command->error('Exam with ID 1 not found. Please seed exams first. Skipping ExamSubmissionSeeder.');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return;
        }

        $questions = $exam->questions()->with('options')->get();
        $mentees = User::where('role_id', 3)->get();
        
        if ($mentees->isEmpty()) {
            $this->command->error('No mentees found. Skipping ExamSubmissionSeeder.');
            DB::statement('SET_FOREIGN_KEY_CHECKS=1;');
            return;
        }

        $this->command->getOutput()->progressStart($mentees->count());

        $submissionsToCreate = [];
        $answersToCreate = [];

        foreach ($mentees as $mentee) {
            $isSubmitted = rand(1, 100) > 5; // 95% chance to be 'graded', 5% 'submitted'
            $score = $isSubmitted ? rand(55, 98) : null;
            $status = $isSubmitted ? 'graded' : 'submitted';
            $submittedAt = Carbon::now()->subDays(rand(1, 30));

            $submissionId = $mentee->id; // Temporary ID, will be replaced after insert

            $submissionsToCreate[] = [
                'mentee_id' => $mentee->id,
                'exam_id' => $exam->id,
                'submitted_at' => $submittedAt,
                'total_score' => $score,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Only create detailed answers for the small subset that need grading
            if ($status === 'submitted') {
                foreach ($questions as $question) {
                    $selectedOptionId = null;
                    $answerText = null;

                    if ($question->type === 'multiple_choice' && $question->options->isNotEmpty()) {
                        $randomOption = $question->options->random();
                        $selectedOptionId = $randomOption->id;
                    } else {
                        $answerText = 'Ini adalah jawaban esai dummy.';
                    }
                    
                    // We can't link this yet because we don't have the submission ID.
                    // For simplicity in this bulk seeder, we will skip adding detailed answers.
                    // The grading form will just show "no answers".
                    // The previous version of the seeder can be used for more detailed testing.
                }
            }

            $this->command->getOutput()->progressAdvance();
        }

        // Bulk insert for efficiency
        ExamSubmission::insert($submissionsToCreate);

        $this->command->getOutput()->progressFinish();
        $this->command->info('Successfully created exam submissions for all ' . $mentees->count() . ' mentees.');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}