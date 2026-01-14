<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentor_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['TFM', 'Diklat']);
            $table->text('description')->nullable();
            $table->date('schedule_date');
            $table->string('schedule_time')->nullable(); // e.g., "09:00 - 12:00"
            $table->string('material_link')->nullable(); // URL or path to a file
            $table->string('test_link')->nullable(); // URL for pre/post-test
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_trainings');
    }
};