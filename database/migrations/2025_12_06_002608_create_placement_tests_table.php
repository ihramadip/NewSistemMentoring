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
        Schema::create('placement_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentee_id')->unique()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedTinyInteger('audio_reading_score')->nullable(); // 0-100
            $table->unsignedTinyInteger('theory_score')->nullable(); // 0-100
            $table->foreignId('final_level_id')->nullable()->constrained('levels')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_tests');
    }
};
