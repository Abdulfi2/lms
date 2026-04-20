<?php
// database/migrations/2024_01_01_000036_create_threads_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id');
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_solved')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('reply_count')->default(0);
            $table->timestamp('last_post_at')->nullable();
            $table->unsignedBigInteger('last_post_user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_post_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('slug');
            $table->index(['forum_id', 'is_pinned', 'last_post_at']);
            $table->index('user_id');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};