<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\ProgressReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('attendances')->truncate();
        DB::table('progress_reports')->truncate();
        Schema::enableForeignKeyConstraints();

        $menteeUser = User::where('email', 'mentee@example.com')->first();
        $sessions = Session::all();

        if ($menteeUser && $sessions->isNotEmpty()) {
            foreach ($sessions as $session) {
                // Create sample attendance
                Attendance::create([
                    'session_id' => $session->id,
                    'mentee_id' => $menteeUser->id,
                    'status' => ($session->session_number <= 2) ? 'hadir' : 'absen', // Present for past sessions, absent for future
                    'notes' => ($session->session_number == 1) ? 'Aktif bertanya' : null,
                ]);

                // Create sample progress report for past sessions
                if ($session->session_number <= 2) {
                    ProgressReport::create([
                        'session_id' => $session->id,
                        'mentee_id' => $menteeUser->id,
                        'score' => rand(70, 95),
                        'reading_notes' => 'Peningkatan tajwid',
                        'general_notes' => 'Perlu lebih banyak latihan',
                    ]);
                }
            }
        }
    }
}
