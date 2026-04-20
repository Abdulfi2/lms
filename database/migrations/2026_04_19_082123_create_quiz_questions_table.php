<?php
// database/migrations/2024_01_01_000026_create_quiz_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->string('title')->nullable();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay', 'matching', 'ordering', 'fill_blank'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->text('hint')->nullable();
            $table->text('explanation')->nullable();
            $table->string('media_url')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->index(['quiz_id', 'order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};