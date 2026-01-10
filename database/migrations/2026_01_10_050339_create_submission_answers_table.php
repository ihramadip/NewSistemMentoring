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
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_submission_id')->constrained('exam_submissions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('chosen_option_id')->nullable()->constrained('options')->cascadeOnUpdate()->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->integer('score')->nullable(); // Score for this specific question within the submission
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
    }
};
