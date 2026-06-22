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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->change();
            $table->boolean('is_final_exam')->default(false)->after('is_practice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->foreignId('section_id')->nullable(false)->change();
            $table->dropColumn('is_final_exam');
        });
    }
};
