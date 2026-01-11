<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('faculties')->truncate();
        Schema::enableForeignKeyConstraints();

        // This list is duplicated from DummyMenteeSeeder to ensure consistency.
        $programs = [
            ['fakultas' => 'Syariah'], ['fakultas' => 'Syariah'], ['fakultas' => 'Syariah'],
            ['fakultas' => 'Dakwah'],
            ['fakultas' => 'Tarbiyah & Keguruan'], ['fakultas' => 'Tarbiyah & Keguruan'],
            ['fakultas' => 'Hukum'],
            ['fakultas' => 'Psikologi'],
            ['fakultas' => 'MIPA'], ['fakultas' => 'MIPA'], ['fakultas' => 'MIPA'],
            ['fakultas' => 'Teknik'], ['fakultas' => 'Teknik'], ['fakultas' => 'Teknik'],
            ['fakultas' => 'Ilmu Komunikasi'],
            ['fakultas' => 'Ekonomi & Bisnis'], ['fakultas' => 'Ekonomi & Bisnis'], ['fakultas' => 'Ekonomi & Bisnis'],
            ['fakultas' => 'Kedokteran'],
        ];

        // Get unique faculty names
        $facultyNames = array_unique(array_column($programs, 'fakultas'));

        foreach ($facultyNames as $name) {
            Faculty::create(['name' => $name]);
        }
    }
}
