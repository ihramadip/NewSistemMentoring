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

        $faculties = [
            ['name' => 'Psikologi'],
            ['name' => 'Ilmu Komputer'],
            ['name' => 'Ekonomi'],
            ['name' => 'Kedokteran'],
            ['name' => 'Teknik'],
            ['name' => 'Hukum'],
        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}
