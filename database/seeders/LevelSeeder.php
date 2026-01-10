<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('levels')->truncate();
        Schema::enableForeignKeyConstraints();

        $levels = [
            ['name' => 'Fasih'],
            ['name' => 'Ibtida'],
            ['name' => 'Hijaiyah 1'],
            ['name' => 'Hijaiyah 2'],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
