<?php
// database/migrations/2024_01_01_000023_create_assignment_rubrics_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_rubrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->string('criteria');
            $table->text('description')->nullable();
            $table->integer('max_points');
            $table->float('weight')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->index('assignment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_rubrics');
    }
};