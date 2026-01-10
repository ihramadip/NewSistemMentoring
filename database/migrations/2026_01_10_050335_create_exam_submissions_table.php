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
        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable(); // Can be null if ongoing
            $table->integer('total_score')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded', 'overdue'])->default('pending');
            $table->timestamps();

            $table->unique(['mentee_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_submissions');
    }
};
