<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentoringGroup;
use App\Models\User;
use App\Models\Level;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MentoringGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('mentoring_groups')->truncate();
        Schema::enableForeignKeyConstraints();

        // Get the test mentor user
        $mentorUser = User::where('email', 'mentor@example.com')->first();
        // Get a sample level (e.g., Fasih)
        $level = Level::where('name', 'Fasih')->first();

        if ($mentorUser && $level) {
            MentoringGroup::create([
                'name' => 'Kelompok Mentoring Fasih 1',
                'mentor_id' => $mentorUser->id,
                'level_id' => $level->id,
                'schedule_info' => 'Setiap Selasa, 14:00 - 15:00 via Zoom',
            ]);
        }
    }
}
