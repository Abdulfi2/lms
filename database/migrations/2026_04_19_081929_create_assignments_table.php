<?php
// database/migrations/2024_01_01_000022_create_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('instructions')->nullable();
            $table->integer('max_score')->default(100);
            $table->integer('passing_score')->default(60);
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->timestamp('due_date');
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_penalty')->default(0)->comment('Penalty percentage per day');
            $table->string('attachment')->nullable();
            $table->boolean('is_group_assignment')->default(false);
            $table->integer('max_group_size')->nullable();
            $table->boolean('enable_ai_check')->default(false);
            $table->boolean('enable_plagiarism_check')->default(false);
            $table->boolean('enable_peer_review')->default(false);
            $table->json('rubric')->nullable();
            $table->integer('total_submissions')->default(0);
            $table->integer('graded_count')->default(0);
            $table->float('average_score')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            $table->index(['course_id', 'due_date']);
            $table->index('due_date');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};