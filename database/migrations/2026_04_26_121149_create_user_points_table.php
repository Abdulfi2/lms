<?php
// database/migrations/xxxx_xx_xx_create_user_points_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('total_points')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('total_courses_completed')->default(0);
            $table->integer('total_quizzes_passed')->default(0);
            $table->integer('total_assignments_submitted')->default(0);
            $table->integer('total_forum_posts')->default(0);
            $table->integer('streak_days')->default(0);
            $table->date('last_activity_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_points');
    }
};