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
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->renameColumn('topic', 'title');
            $table->text('description')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->renameColumn('title', 'topic');
            $table->dropColumn('description');
        });
    }
};