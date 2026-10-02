<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'rejected'])->default('paid')->after('instructor_id');
        });

        // doctrine/dbal tidak terpasang, gunakan raw SQL untuk mengubah paid_at jadi nullable
        // (dibutuhkan karena permintaan payout dari instruktur belum punya tanggal pencairan).
        // MySQL-only syntax: SQLite (dipakai untuk testing) tidak mendukung ALTER ... MODIFY
        // dan sudah memperlakukan kolom ini sebagai nullable di skema fresh, jadi cukup di-skip.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE payouts MODIFY paid_at TIMESTAMP NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE payouts MODIFY paid_at TIMESTAMP NOT NULL');
        }
    }
};
