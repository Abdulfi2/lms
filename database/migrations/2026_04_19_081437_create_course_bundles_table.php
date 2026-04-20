<?php
// database/migrations/2024_01_01_000016_create_course_bundles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('bundle_price', 12, 2);
            $table->decimal('original_price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('total_courses')->default(0);
            $table->integer('total_students')->default(0);
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_bundles');
    }
};