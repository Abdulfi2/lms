<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();

            // Spatie will add 'role' column? No, Spatie uses separate table
            // But we keep a default role column for quick access
            $table->string('default_role')->nullable()->comment('Quick access role from Spatie');

            // Account Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Security
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->timestamp('last_seen_at')->nullable();

            // Remember Token & Timestamps
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['email', 'is_active']);
            $table->index('default_role');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};