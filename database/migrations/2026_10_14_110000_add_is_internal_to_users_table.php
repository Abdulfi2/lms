<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pembeda user internal (karyawan Zakat Sukses) vs external (publik) - independen
            // dari role, karena karyawan bisa saja terdaftar dengan role apa pun (student,
            // instructor, dst), bukan role tersendiri.
            $table->boolean('is_internal')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_internal');
        });
    }
};
