<?php
// database/migrations/2024_01_01_000012_create_taggables_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('taggable_id');
            $table->string('taggable_type');
            $table->timestamps();
            
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->index(['taggable_id', 'taggable_type']);
            $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'taggable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};