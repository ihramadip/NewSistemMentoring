<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentoringGroup;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('mentoring_sessions')->truncate(); // Truncate the correct table name
        Schema::enableForeignKeyConstraints();

        $mentoringGroup = MentoringGroup::where('name', 'Kelompok Mentoring Fasih 1')->first();

        if ($mentoringGroup) {
            // Create 3 sample sessions for the group
            Session::create([
                'mentoring_group_id' => $mentoringGroup->id,
                'session_number' => 1,
                'date' => Carbon::now()->subDays(7), // 1 week ago
                'topic' => 'Pengenalan Tajwid dan Makharijul Huruf',
            ]);

            Session::create([
                'mentoring_group_id' => $mentoringGroup->id,
                'session_number' => 2,
                'date' => Carbon::now()->subDays(3), // 3 days ago
                'topic' => 'Hukum Nun Mati dan Tanwin',
            ]);

            Session::create([
                'mentoring_group_id' => $mentoringGroup->id,
                'session_number' => 3,
                'date' => Carbon::now()->addDays(2), // 2 days in the future
                'topic' => 'Hukum Mim Mati',
            ]);
        }
    }
}
