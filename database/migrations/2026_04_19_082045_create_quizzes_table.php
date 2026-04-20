<?php
// database/migrations/2024_01_01_000025_create_quizzes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->integer('attempts_allowed')->default(1);
            $table->integer('passing_score')->default(70);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('show_correct_answers')->default(false);
            $table->boolean('show_explanation')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('total_questions')->default(0);
            $table->integer('total_attempts')->default(0);
            $table->float('average_score')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            $table->index('course_id');
            $table->index('is_published');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};