<?php
// database/migrations/2024_01_01_000020_create_lesson_resources_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->nullable()->comment('In bytes');
            $table->string('file_type');
            $table->integer('download_count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->index('lesson_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_resources');
    }
};