<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * events.speaker_bio di-set VARCHAR(255) sejak awal, tapi form create/edit event
     * menampilkannya sebagai textarea multi-baris tanpa batas panjang - bio pembicara
     * yang lebih dari 255 karakter bikin gagal simpan dengan "Data too long for column
     * 'speaker_bio'" (baik saat membuat maupun mengedit event, termasuk saat upload
     * gambar event bersamaan karena satu form yang sama).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE events MODIFY COLUMN speaker_bio TEXT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Potong balik ke 255 karakter supaya tidak gagal kalau ada baris yang sudah
            // lebih panjang dari itu sejak kolom dilebarkan.
            DB::statement('UPDATE events SET speaker_bio = LEFT(speaker_bio, 255) WHERE CHAR_LENGTH(speaker_bio) > 255');
            DB::statement('ALTER TABLE events MODIFY COLUMN speaker_bio VARCHAR(255) NULL');
        }
    }
};
