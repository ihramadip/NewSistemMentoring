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
        Schema::create('additional_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentoring_group_id')->constrained('mentoring_groups')->onDelete('cascade');
            $table->string('topic');
            $table->date('date');
            $table->enum('status', ['sudah', 'belum'])->default('belum');
            $table->string('proof_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_sessions');
    }
};