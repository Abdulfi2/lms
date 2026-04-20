<?php
// database/migrations/2024_01_01_000041_create_user_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->json('email_notifications')->nullable();
            $table->json('push_notifications')->nullable();
            $table->boolean('dark_mode')->default(false);
            $table->boolean('compact_view')->default(false);
            $table->boolean('auto_play_video')->default(true);
            $table->boolean('show_subtitles')->default(false);
            $table->string('video_quality')->default('auto');
            $table->json('accessibility_settings')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};