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
        Schema::create('mentoring_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('level_id')->constrained('levels')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('schedule_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentoring_groups');
    }
};
