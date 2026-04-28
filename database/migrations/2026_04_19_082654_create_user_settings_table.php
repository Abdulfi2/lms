<?php
// database/migrations/xxxx_xx_xx_create_user_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            // General preferences
            $table->string('language')->default('id');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('date_format')->default('d/m/Y');
            $table->boolean('dark_mode')->default(false);
            $table->boolean('compact_view')->default(false);

            // Notification preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('assignment_reminder')->default(true);
            $table->boolean('quiz_reminder')->default(true);
            $table->boolean('course_update_notification')->default(true);
            $table->boolean('forum_reply_notification')->default(true);
            $table->boolean('certificate_notification')->default(true);
            $table->boolean('event_reminder')->default(true);

            // Learning preferences
            $table->boolean('auto_play_video')->default(true);
            $table->boolean('show_subtitles')->default(false);
            $table->string('video_quality')->default('auto');
            $table->boolean('auto_mark_complete')->default(false);
            $table->integer('daily_goal_minutes')->default(30);

            // Privacy settings
            $table->boolean('profile_public')->default(true);
            $table->boolean('show_progress')->default(true);
            $table->boolean('show_certificates')->default(true);
            $table->boolean('allow_messages')->default(true);

            // Accessibility
            $table->boolean('high_contrast')->default(false);
            $table->boolean('large_text')->default(false);
            $table->boolean('screen_reader')->default(false);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};