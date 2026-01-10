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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('mentoring_sessions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('status', ['hadir', 'izin', 'absen'])->default('absen');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'mentee_id']); // satu record per mentee per sesi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
