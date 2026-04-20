<?php
// database/migrations/2024_01_01_000021_create_lesson_completions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->boolean('is_completed')->default(true);
            $table->timestamp('completed_at')->useCurrent();
            $table->integer('time_spent')->default(0)->comment('Time spent in seconds');
            $table->integer('watch_percentage')->default(0);
            $table->integer('last_position')->default(0)->comment('Last video position in seconds');
            $table->text('notes')->nullable();
            $table->integer('rating')->nullable()->comment('Lesson rating 1-5');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->unique(['user_id', 'lesson_id']);
            $table->index(['user_id', 'is_completed']);
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
    }
};