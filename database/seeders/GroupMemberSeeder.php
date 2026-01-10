<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentoringGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GroupMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('group_members')->truncate();
        Schema::enableForeignKeyConstraints();

        // Get the test mentee user
        $menteeUser = User::where('email', 'mentee@example.com')->first();
        // Get the sample mentoring group
        $mentoringGroup = MentoringGroup::where('name', 'Kelompok Mentoring Fasih 1')->first();

        if ($menteeUser && $mentoringGroup) {
            // Attach the mentee to the group
            $mentoringGroup->members()->attach($menteeUser->id);
        }
    }
}
