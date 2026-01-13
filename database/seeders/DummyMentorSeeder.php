<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Faculty;
use App\Models\MentorApplication;
use App\Models\MentoringGroup;
use App\Models\Role;

class DummyMentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy mentors...');

        // Find role IDs
        $mentorRole = Role::where('name', 'Mentor')->first();
        $menteeRole = Role::where('name', 'Mentee')->first();

        if (!$mentorRole || !$menteeRole) {
            $this->command->error('Mentor or Mentee role not found. Please run RoleSeeder first.');
            return;
        }

        // --- Calculation ---
        $totalMentees = User::where('role_id', $menteeRole->id)->count();
        $menteesPerGroup = 14;
        $numberOfMentors = ceil($totalMentees / $menteesPerGroup);

        $this->command->info("Total mentees: $totalMentees. Required mentors: $numberOfMentors");

        // --- Data Preparation ---
        $faculties = Faculty::all();
        if ($faculties->isEmpty()) {
            $this->command->error('No faculties found. Please run FacultySeeder first.');
            return;
        }
        
        $programStudiList = [
            'Teknik Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi', 'Ilmu Komunikasi', 'Psikologi', 
            'Desain Komunikasi Visual', 'Hukum', 'Pendidikan Guru Sekolah Dasar', 'Sastra Inggris'
        ];
        
        // --- Deletion of Old Data ---
        $this->command->warn('Deleting old mentor data...');
        $oldMentorUsers = User::where('role_id', $mentorRole->id)->get();
        if ($oldMentorUsers->isNotEmpty()) {
            $oldMentorIds = $oldMentorUsers->pluck('id');
            
            // Delete dependent records first
            MentoringGroup::whereIn('mentor_id', $oldMentorIds)->delete();
            MentorApplication::whereIn('user_id', $oldMentorIds)->delete();
            
            // Now delete the users
            User::whereIn('id', $oldMentorIds)->delete();
        }
        $this->command->info('Old mentor data deleted.');


        // --- Seeding ---
        $progressBar = $this->command->getOutput()->createProgressBar($numberOfMentors);
        $progressBar->start();

        for ($i = 0; $i < $numberOfMentors; $i++) {
            $faculty = $faculties->random();
            $gender = ['male', 'female'][array_rand(['male', 'female'])];
            
            // Create a unique NPM for mentor
            $npm = '99' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT) . str_pad($i, 4, '0', STR_PAD_LEFT);

            $user = User::create([
                'name' => fake()->name($gender == 'male' ? 'male' : 'female'),
                'email' => $npm . '@gmail.com',
                'password' => Hash::make($npm),
                'npm' => $npm,
                'role_id' => $mentorRole->id,
                'faculty_id' => $faculty->id,
                'program_study' => $programStudiList[array_rand($programStudiList)],
                'gender' => $gender,
            ]);

            MentorApplication::create([
                'user_id' => $user->id,
                'btaq_history' => 'Dummy BTAQ history from seeder.',
                'status' => 'accepted',
                'notes_from_reviewer' => 'Automatically accepted by seeder.',
                'cv_path' => 'dummy/cv.pdf',
                'recording_path' => 'dummy/recording.mp3',
            ]);

            $progressBar->advance();
        }


        $progressBar->finish();
        $this->command->info("\nDummy mentors and their accepted applications seeded successfully.");
    }
}