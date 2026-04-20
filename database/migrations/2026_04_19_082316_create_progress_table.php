<?php
// database/migrations/2024_01_01_000031_create_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('total_lessons')->default(0);
            $table->integer('completed_lessons')->default(0);
            $table->float('percentage')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->unsignedBigInteger('last_lesson_id')->nullable();
            $table->integer('last_position')->default(0);
            $table->integer('total_time_spent')->default(0)->comment('Total time spent in seconds');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('last_lesson_id')->references('id')->on('lessons')->onDelete('set null');
            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'percentage']);
            $table->index(['course_id', 'percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};