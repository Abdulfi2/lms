<?php
// database/migrations/2024_01_01_000013_create_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 255)->nullable();
            $table->longText('description');
            $table->json('requirements')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->json('target_audience')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('video_promo')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('beginner');
            $table->string('language')->default('en');
            $table->integer('duration_total')->default(0)->comment('Total hours');
            $table->integer('total_lessons')->default(0);
            $table->integer('total_sections')->default(0);
            $table->integer('total_students')->default(0);
            $table->float('average_rating')->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('enrolled_count')->default(0);
            $table->integer('wishlist_count')->default(0);
            $table->enum('status', ['draft', 'pending', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_certificate')->default(false);
            $table->string('certificate_template')->nullable();
            $table->integer('max_students')->nullable();
            $table->timestamp('enrollment_end_date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('access_days')->nullable()->comment('Days after enrollment');
            $table->json('prerequisites')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedBigInteger('instructor_id');
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('slug');
            $table->index('status');
            $table->index('level');
            $table->index('price');
            $table->index('is_featured');
            $table->index('instructor_id');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};