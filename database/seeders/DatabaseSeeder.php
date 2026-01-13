<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(FacultySeeder::class);
        $this->call(LevelSeeder::class);

        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $mentorRole = Role::where('name', 'Mentor')->first();
        $menteeRole = Role::where('name', 'Mentee')->first();

        // Get a faculty (assuming at least one exists after FacultySeeder)
        $defaultFaculty = Faculty::first();

        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'npm' => 'ADM001',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'faculty_id' => $defaultFaculty ? $defaultFaculty->id : null,
                'gender' => 'male',
                'email_verified_at' => now(),
            ]
        );

        // Create Mentor User
        User::firstOrCreate(
            ['email' => 'mentor@example.com'],
            [
                'name' => 'Mentor User',
                'npm' => 'MTR001',
                'password' => Hash::make('password'),
                'role_id' => $mentorRole->id,
                'faculty_id' => $defaultFaculty ? $defaultFaculty->id : null,
                'gender' => 'female',
                'email_verified_at' => now(),
            ]
        );

        // Create Mentee User (update existing test user or create new)
        User::firstOrCreate(
            ['email' => 'mentee@example.com'],
            [
                'name' => 'Mentee User',
                'npm' => 'MTI001',
                'password' => Hash::make('password'),
                'role_id' => $menteeRole->id,
                'faculty_id' => $defaultFaculty ? $defaultFaculty->id : null,
                'gender' => 'male',
                'email_verified_at' => now(),
            ]
        );

        // Generate a large pool of dummy mentees and their placement tests
        $this->call(DummyMenteeSeeder::class);

        // Call Exam Submission Seeder to create submissions for the new mentees
        $this->call(ExamSubmissionSeeder::class);

        // Generate dummy mentors based on mentee count
        $this->call(DummyMentorSeeder::class);

        // Call Mentoring Group and Group Member seeders after users are created
        // $this->call(MentoringGroupSeeder::class);
        // $this->call(GroupMemberSeeder::class);

        // Call Session, Attendance, and Progress Report seeders after groups are created
        // $this->call(SessionSeeder::class);
        // $this->call(AttendanceProgressSeeder::class);

    }
}

