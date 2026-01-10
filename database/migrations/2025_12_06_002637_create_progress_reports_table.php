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
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('mentoring_sessions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedTinyInteger('score')->nullable(); // 0-100
            $table->text('reading_notes')->nullable();
            $table->text('general_notes')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'mentee_id']); // satu laporan per mentee per sesi
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_reports');
    }
};
