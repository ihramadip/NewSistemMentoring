<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlacementTestDefinition;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class PlacementTestQuestionSeeder extends Seeder
{
    /**
     * The set of questions to be seeded.
     *
     * @var array
     */
    private $theory_questions = [
        1 => ['question' => 'Apa hukum bacaan Nun Sukun bertemu dengan huruf Ba (ب)?', 'options' => ['Idgham', 'Iqlab', 'Ikhfa', 'Izhar'], 'answer' => 'Iqlab', 'score' => 20],
        2 => ['question' => 'Berikut ini yang termasuk huruf Qalqalah adalah...', 'options' => ['ق', 'ل', 'م', 'n'], 'answer' => 'ق', 'score' => 20],
        3 => ['question' => 'Membaca Al-Qur\'an dengan tartil artinya...', 'options' => ['Cepat dan lancar', 'Perlahan dan jelas', 'Dengan suara keras', 'Dengan irama'], 'answer' => 'Perlahan dan jelas', 'score' => 20],
        4 => ['question' => 'Apa yang dimaksud dengan Mad Wajib Muttasil?', 'options' => ['Mad bertemu hamzah dalam satu kata', 'Mad bertemu hamzah di lain kata', 'Mad bertemu sukun', 'Mad bertemu tasydid'], 'answer' => 'Mad bertemu hamzah dalam satu kata', 'score' => 20],
        5 => ['question' => 'Berapa harakat panjang bacaan Mad Jaiz Munfasil?', 'options' => ['2 harakat', '4 atau 5 harakat', '6 harakat', '2, 4, atau 6 harakat'], 'answer' => '4 atau 5 harakat', 'score' => 20],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Create or find the placement test definition
            $placementTestDefinition = PlacementTestDefinition::firstOrCreate(['name' => 'Default Placement Test']);

            // 2. Clear existing questions to avoid duplicates on re-seed
            $placementTestDefinition->questions()->delete();

            $this->command->getOutput()->progressStart(count($this->theory_questions));

            // 3. Seed the new questions and options
            foreach ($this->theory_questions as $questionData) {
                $question = $placementTestDefinition->questions()->create([
                    'question_text' => $questionData['question'],
                    'question_type' => 'multiple_choice',
                    'score_value' => $questionData['score'],
                ]);

                foreach ($questionData['options'] as $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => ($optionText === $questionData['answer']),
                    ]);
                }
                $this->command->getOutput()->progressAdvance();
            }
             $this->command->getOutput()->progressFinish();
        });

        $this->command->info('Placement test questions seeded successfully!');
    }
}