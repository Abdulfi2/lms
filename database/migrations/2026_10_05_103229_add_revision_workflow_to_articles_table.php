<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alur review editor: draft -> revision (dikembalikan ke penulis dengan
        // catatan) atau ready_to_publish (disetujui, tinggal terbit) -> published.
        // MySQL menegakkan ENUM di level kolom jadi perlu ALTER manual; driver
        // lain (mis. SQLite di test suite) tidak punya tipe ENUM asli — kolomnya
        // sudah berupa varchar tanpa CHECK constraint dari migrasi awal, jadi
        // nilai status baru otomatis diterima tanpa perlu diubah.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'revision', 'ready_to_publish', 'published', 'archived') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pindahkan dulu baris yang masih berstatus revision/ready_to_publish ke
        // 'draft' sebelum ENUM diperkecil — tanpa ini, MODIFY COLUMN akan gagal
        // (strict mode) atau diam-diam mengosongkan status baris tsb (non-strict).
        if (DB::getDriverName() === 'mysql') {
            DB::table('articles')
                ->whereIn('status', ['revision', 'ready_to_publish'])
                ->update(['status' => 'draft']);
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('revision_notes');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft'");
        }
    }
};
