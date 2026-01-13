<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\ProgressReport;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MenteeProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy attendance and progress reports for all sessions...');

        $faker = Faker::create('id_ID');
        
        // Get all sessions with the members of the group they belong to
        $sessions = Session::with('mentoringGroup.members')->get();

        if ($sessions->isEmpty()) {
            $this->command->warn('No sessions found. Skipping progress report seeding.');
            return;
        }

        $allNewAttendances = [];
        $allNewProgressReports = [];
        $now = now();

        // Get existing records to avoid duplicates
        $existingAttendances = Attendance::select('session_id', 'mentee_id')->get()->keyBy(function ($item) {
            return $item->session_id . '-' . $item->mentee_id;
        });

        $existingProgressReports = ProgressReport::select('session_id', 'mentee_id')->get()->keyBy(function ($item) {
            return $item->session_id . '-' . $item->mentee_id;
        });

        $this->command->getOutput()->progressStart($sessions->count());

        foreach ($sessions as $session) {
            if ($session->mentoringGroup && $session->mentoringGroup->members->isNotEmpty()) {
                foreach ($session->mentoringGroup->members as $mentee) {
                    $attendanceKey = $session->id . '-' . $mentee->id;
                    
                    // Check if attendance already exists
                    if (!$existingAttendances->has($attendanceKey)) {
                        $status = $faker->randomElement(['hadir', 'hadir', 'hadir', 'hadir', 'absen', 'izin']);
                        
                        $allNewAttendances[] = [
                            'session_id' => $session->id,
                            'mentee_id' => $mentee->id,
                            'status' => $status,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        // If mentee was present, create a progress report
                        $reportKey = $session->id . '-' . $mentee->id;
                        if ($status === 'hadir' && !$existingProgressReports->has($reportKey)) {
                            $allNewProgressReports[] = [
                                'session_id' => $session->id,
                                'mentee_id' => $mentee->id,
                                'score' => $faker->numberBetween(65, 100),
                                'reading_notes' => $faker->sentence(),
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }
            }
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        if (!empty($allNewAttendances)) {
            $this->command->info('Inserting ' . count($allNewAttendances) . ' new attendance records...');
            foreach (array_chunk($allNewAttendances, 500) as $chunk) {
                Attendance::insert($chunk);
            }
        } else {
            $this->command->info('No new attendance records to add.');
        }

        if (!empty($allNewProgressReports)) {
            $this->command->info('Inserting ' . count($allNewProgressReports) . ' new progress reports...');
            foreach (array_chunk($allNewProgressReports, 500) as $chunk) {
                ProgressReport::insert($chunk);
            }
        } else {
            $this->command->info('No new progress reports to add.');
        }

        $this->command->info('Successfully seeded dummy attendance and progress reports.');
    }
}