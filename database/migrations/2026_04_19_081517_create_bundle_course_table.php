<?php
// database/migrations/2024_01_01_000017_create_bundle_course_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_course', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('bundle_id')->references('id')->on('course_bundles')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['bundle_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_course');
    }
};