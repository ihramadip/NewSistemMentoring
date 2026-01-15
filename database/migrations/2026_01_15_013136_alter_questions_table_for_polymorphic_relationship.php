<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new columns first, making them nullable
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('questionable_id')->nullable()->after('id');
            $table->string('questionable_type')->nullable()->after('questionable_id');
            $table->index(['questionable_id', 'questionable_type']);
        });

        // Now that the columns exist, populate them
        DB::table('questions')->whereNotNull('exam_id')->update([
            'questionable_id' => DB::raw('exam_id'),
            'questionable_type' => Exam::class,
        ]);

        // Finally, drop the old column
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropColumn('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable()->after('id');
        });

        DB::table('questions')
            ->where('questionable_type', Exam::class)
            ->update(['exam_id' => DB::raw('questionable_id')]);

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['questionable_id', 'questionable_type']);
            // If you need to restore the foreign key constraint, you might need to handle
            // cases where the related exam has been deleted.
            // For this non-production scenario, we'll omit adding it back to avoid complexity.
        });
    }
};
