<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // due_date sudah divalidasi sebagai 'nullable' di AssignmentController, tapi
        // kolomnya masih NOT NULL tanpa default — bikin insert gagal (500) saat
        // instruktur membuat tugas tanpa mengisi deadline. doctrine/dbal tidak
        // terpasang di project ini, jadi pakai raw SQL alih-alih Schema::change().
        DB::statement('ALTER TABLE assignments MODIFY due_date TIMESTAMP NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE assignments MODIFY due_date TIMESTAMP NOT NULL');
    }
};
