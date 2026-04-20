<?php
// database/migrations/2024_01_01_000019_create_lessons_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->longText('content_html')->nullable();
            $table->enum('type', ['video', 'article', 'quiz', 'assignment', 'live', 'discussion'])->default('video');
            $table->integer('duration')->default(0)->comment('Duration in minutes');
            $table->integer('order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_required')->default(true);
            $table->integer('points')->default(0);
            $table->string('video_url')->nullable();
            $table->string('video_id')->nullable()->comment('YouTube/Vimeo video ID');
            $table->text('video_embed')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_downloadable')->default(false);
            $table->boolean('has_preview')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->index(['section_id', 'order']);
            $table->index('type');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};