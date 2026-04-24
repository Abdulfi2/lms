<?php
// database/migrations/2024_01_01_000027_create_quiz_options_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->integer('points')->default(0);
            $table->string('matching_pair')->nullable();
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('quiz_question_id'); // <-- column name is question_id
            $table->timestamps();

            $table->foreign('quiz_question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
            $table->index(['quiz_question_id', 'is_correct']);
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};