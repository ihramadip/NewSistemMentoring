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
        Schema::create('mentoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentoring_group_id')->constrained('mentoring_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedTinyInteger('session_number'); // 1..7
            $table->date('date')->nullable();
            $table->string('topic')->nullable();
            $table->timestamps();

            $table->unique(['mentoring_group_id', 'session_number']); // satu nomor per group

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentoring_sessions');
    }
};
