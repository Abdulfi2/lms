<?php
// database/migrations/2024_01_01_000037_create_posts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thread_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->json('attachments')->nullable();
            $table->boolean('is_solution')->default(false);
            $table->integer('like_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['thread_id', 'created_at']);
            $table->index('user_id');
            $table->index('is_solution');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};