<?php

namespace App\Services;

use App\Models\User;
use App\Models\PlacementTest;
use App\Models\PlacementTestDefinition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlacementTestService
{
    /**
     * Handle the submission of the placement test.
     *
     * @param User $user The user submitting the test.
     * @param array $answers The user's answers to the theory questions.
     * @param UploadedFile $audioFile The user's audio recording.
     * @return PlacementTest The created placement test submission record.
     * @throws \Exception
     */
    public function handleSubmission(User $user, array $answers, UploadedFile $audioFile): PlacementTest
    {
        $path = null;
        try {
            // Use a transaction to ensure data integrity.
            // If any step fails, the whole process is rolled back.
            $submission = DB::transaction(function () use ($user, $answers, $audioFile, &$path) {

                // 1. Calculate Theory Score
                $theory_score = $this->calculateTheoryScore($answers);

                // 2. Store Audio File
                // The file is stored in a private directory.
                $path = $audioFile->store('placement-tests/audio', 'local');
                if (!$path) {
                    throw new \Exception("Failed to store audio file for user {$user->id}.");
                }

                // 3. Create Placement Test Record
                return PlacementTest::create([
                    'mentee_id' => $user->id,
                    'audio_recording_path' => $path,
                    'theory_score' => $theory_score,
                    'audio_reading_score' => null, // To be graded by admin
                    'final_level_id' => null,      // To be assigned by admin
                ]);
            });

            return $submission;

        } catch (\Throwable $e) {
            // If the transaction fails, attempt to clean up the orphaned audio file.
            if ($path && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            // Log the detailed error for debugging.
            Log::error('Placement test submission failed for user ' . $user->id . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to be handled by the controller.
            throw new \Exception('An unexpected error occurred during test submission. Please try again.');
        }
    }

    /**
     * Calculate the theory score based on user answers.
     *
     * @param array $userAnswers
     * @return float
     */
    private function calculateTheoryScore(array $userAnswers): float
    {
        // In a real-world scenario, you might want to cache this.
        $definition = PlacementTestDefinition::with('questions.options')->firstOrFail();
        
        $score = 0;
        $totalValue = 0;

        foreach ($definition->questions as $question) {
            $totalValue += $question->score_value;
            $correctOption = $question->options->firstWhere('is_correct', true);

            if ($correctOption && isset($userAnswers[$question->id]) && $userAnswers[$question->id] === $correctOption->option_text) {
                $score += $question->score_value;
            }
        }

        if ($totalValue === 0) {
            return 0;
        }

        // The score is calculated based on the sum of score_values, not the count of questions.
        return ($score / $totalValue) * 100;
    }
}
