<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use App\Models\Level;
use App\Models\GroupMember;
use App\Models\PlacementTest;
use App\Models\ExamSubmission;
use App\Models\SubmissionAnswer;
use App\Models\Attendance;
use App\Models\ProgressReport;

class DummyMenteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy mentees and their placement tests...');
        $faker = Faker::create('id_ID');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Cleanup
        $this->command->info('Clearing old mentee data...');
        $menteeRole = Role::where('name', 'Mentee')->first();
        if ($menteeRole) {
            $menteeIds = User::where('role_id', $menteeRole->id)->pluck('id');
            if ($menteeIds->isNotEmpty()) {
                GroupMember::whereIn('mentee_id', $menteeIds)->delete();
                PlacementTest::whereIn('mentee_id', $menteeIds)->delete();
                $submissionIds = ExamSubmission::whereIn('mentee_id', $menteeIds)->pluck('id');
                if ($submissionIds->isNotEmpty()) {
                    SubmissionAnswer::whereIn('exam_submission_id', $submissionIds)->delete();
                    ExamSubmission::whereIn('id', $submissionIds)->delete();
                }
                Attendance::whereIn('mentee_id', $menteeIds)->delete();
                ProgressReport::whereIn('mentee_id', $menteeIds)->delete();
                User::whereIn('id', $menteeIds)->delete();
            }
        }
        PlacementTest::truncate(); // Also truncate placement tests just in case
        $this->command->info('Old data cleared.');
        
        // 2. Prepare data for user creation
        $programs = [
            ['kode' => '1001', 'nama' => 'Hukum Keluarga (Ahwal Al-Syakhshiyyah)', 'fakultas' => 'Syariah'], ['kode' => '1002', 'nama' => 'Hukum Ekonomi Syariah (Muamalah)', 'fakultas' => 'Syariah'], ['kode' => '1003', 'nama' => 'Perbankan Syariah', 'fakultas' => 'Syariah'], ['kode' => '1004', 'nama' => 'Komunikasi dan Penyiaran Islam', 'fakultas' => 'Dakwah'], ['kode' => '1005', 'nama' => 'Pendidikan Agama Islam', 'fakultas' => 'Tarbiyah & Keguruan'], ['kode' => '1006', 'nama' => 'PG-PAUD', 'fakultas' => 'Tarbiyah & Keguruan'], ['kode' => '1007', 'nama' => 'Ilmu Hukum', 'fakultas' => 'Hukum'], ['kode' => '1008', 'nama' => 'Psikologi', 'fakultas' => 'Psikologi'], ['kode' => '1009', 'nama' => 'Statistika', 'fakultas' => 'MIPA'], ['kode' => '1010', 'nama' => 'Matematika', 'fakultas' => 'MIPA'], ['kode' => '1011', 'nama' => 'Farmasi', 'fakultas' => 'MIPA'], ['kode' => '1012', 'nama' => 'Teknik Pertambangan', 'fakultas' => 'Teknik'], ['kode' => '1013', 'nama' => 'Teknik Industri', 'fakultas' => 'Teknik'], ['kode' => '1014', 'nama' => 'Perencanaan Wilayah & Kota (PWK)', 'fakultas' => 'Teknik'], ['kode' => '1015', 'nama' => 'Ilmu Komunikasi', 'fakultas' => 'Ilmu Komunikasi'], ['kode' => '1016', 'nama' => 'Akuntansi', 'fakultas' => 'Ekonomi & Bisnis'], ['kode' => '1017', 'nama' => 'Manajemen', 'fakultas' => 'Ekonomi & Bisnis'], ['kode' => '1018', 'nama' => 'Ekonomi Pembangunan', 'fakultas' => 'Ekonomi & Bisnis'], ['kode' => '1019', 'nama' => 'Kedokteran', 'fakultas' => 'Kedokteran'],
        ];
        $faculties = Faculty::pluck('id', 'name');
        $prodiCounters = array_fill_keys(array_column($programs, 'kode'), 0);
        $totalMentees = rand(2900, 3000);
        $newUsersData = [];
        $now = now();

        $this->command->info("Preparing {$totalMentees} new mentee users...");
        $userProgressBar = $this->command->getOutput()->createProgressBar($totalMentees);
        $userProgressBar->start();

        for ($i = 0; $i < $totalMentees; $i++) {
            $prodi = $programs[array_rand($programs)];
            $prodiCode = $prodi['kode'];
            $facultyName = $prodi['fakultas'];
            if (!isset($faculties[$facultyName])) continue;
            $year = $faker->numberBetween(23, 25);
            $prodiCounters[$prodiCode]++;
            $sequence = str_pad($prodiCounters[$prodiCode], 5, '0', STR_PAD_LEFT);
            $npm = "{$prodiCode}{$year}{$sequence}";
            $newUsersData[] = [
                'name' => $faker->name, 'npm' => $npm, 'email' => "{$npm}@gmail.com", 'password' => Hash::make($npm), 'role_id' => $menteeRole->id, 'faculty_id' => $faculties[$facultyName], 'program_study' => $prodi['nama'], 'gender' => $faker->randomElement(['male', 'female']), 'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ];
            $userProgressBar->advance();
        }
        $userProgressBar->finish();
        $this->command->newLine(2);


        // 3. Bulk insert users
        $this->command->info('Inserting new users into database...');
        foreach (array_chunk($newUsersData, 500) as $chunk) {
            User::insert($chunk);
        }
        $this->command->info("Successfully created " . count($newUsersData) . " dummy mentee users.");

        // 4. Create Placement Tests for the new users
        $this->command->info('Seeding placement tests for new mentees...');
        $newlyCreatedMentees = User::where('role_id', $menteeRole->id)->get();
        $menteeCount = $newlyCreatedMentees->count();
        $levelMap = Level::pluck('id', 'name');
        $requiredLevels = ['Fasih', 'Ibtida', 'Hijaiyah 1', 'Hijaiyah 2'];
        foreach ($requiredLevels as $levelName) {
            if (!isset($levelMap[$levelName])) {
                $this->command->error("Level '{$levelName}' not found. Aborting."); return;
            }
        }

        $placementTests = [];
        $ptProgressBar = $this->command->getOutput()->createProgressBar($menteeCount);
        $ptProgressBar->start();
        foreach ($newlyCreatedMentees as $mentee) {
            $averageScore = rand(40, 100); // Simplified score generation
            if ($averageScore > 85) $levelId = $levelMap['Fasih'];
            elseif ($averageScore > 70) $levelId = $levelMap['Ibtida'];
            elseif ($averageScore > 55) $levelId = $levelMap['Hijaiyah 1'];
            else $levelId = $levelMap['Hijaiyah 2'];
            $placementTests[] = [
                'mentee_id' => $mentee->id, 'audio_reading_score' => $averageScore - rand(0, 5), 'theory_score' => $averageScore + rand(0, 5), 'final_level_id' => $levelId, 'created_at' => $now, 'updated_at' => $now,
            ];
            $ptProgressBar->advance();
        }
        $ptProgressBar->finish();
        $this->command->newLine(2);

        // 5. Bulk insert placement tests
        foreach (array_chunk($placementTests, 500) as $chunk) {
            PlacementTest::insert($chunk);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Finished seeding ' . count($placementTests) . ' placement tests for ' . $menteeCount . ' mentees.');
    }
}
