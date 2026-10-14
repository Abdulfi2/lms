<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Label baris yang sudah dibuat migrasi sebelumnya; nilai URL yang diisi admin tidak disentuh.
        DB::table('settings')->where('key', 'footer_twitter')->update(['label' => 'Tautan X']);
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'footer_twitter')->update(['label' => 'Tautan X / Twitter']);
        Cache::forget('settings.all');
    }
};
