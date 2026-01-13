<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MentoringGroup;
use App\Models\Session;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MentorSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy sessions for existing mentoring groups...');

        // No deletion of any data as requested.

        $faker = Faker::create('id_ID');
        $groups = MentoringGroup::with('sessions')->get();
        
        if ($groups->isEmpty()) {
            $this->command->warn('No mentoring groups found. Skipping session seeding.');
            return;
        }

        $allNewSessions = [];
        $now = now();

        foreach ($groups as $group) {
            // Only seed sessions if a group has no sessions yet, to avoid duplication on re-runs.
            if ($group->sessions->isEmpty()) {
                $numberOfSessions = rand(5, 7);
                $lastSessionNumber = 0; // Start from 0 for this new group

                for ($i = 1; $i <= $numberOfSessions; $i++) {
                    $allNewSessions[] = [
                        'mentoring_group_id' => $group->id,
                        'session_number' => $lastSessionNumber + $i,
                        'date' => $faker->dateTimeBetween('-1 month', '+1 month'),
                        'title' => 'Sesi ' . $i . ': ' . $faker->sentence(3),
                        'description' => $faker->paragraph(2),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }
        
        if (empty($allNewSessions)) {
            $this->command->info('All groups already have sessions. Nothing to seed.');
            return;
        }

        $this->command->info('Generating ' . count($allNewSessions) . ' new sessions...');

        // Chunk insert for performance
        foreach (array_chunk($allNewSessions, 500) as $chunk) {
            Session::insert($chunk);
        }

        $this->command->info('Successfully seeded dummy sessions for groups that had none.');
    }
}