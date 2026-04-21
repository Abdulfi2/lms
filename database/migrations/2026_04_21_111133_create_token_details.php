<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('token_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_token')->unique();
            $table->string('token', 255)->nullable();
            $table->string('token_type', 255)->nullable();
            $table->dateTime('expires_in')->nullable();
            $table->string('refresh_token', 255)->nullable();
            $table->string('scope', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_details');
    }
};