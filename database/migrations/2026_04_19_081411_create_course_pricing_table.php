<?php
// database/migrations/2024_01_01_000015_create_course_pricing_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('country_code', 2);
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
            
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['course_id', 'country_code']);
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_pricing');
    }
};