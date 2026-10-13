<?php

use App\Support\FooterSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Seeder tidak otomatis dijalankan di server yang sudah berjalan, jadi baris pengaturan
        // footer dibuat di sini. Baris yang sudah ada tidak ditimpa.
        foreach (FooterSettings::defaults() as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();

            if (!$exists) {
                DB::table('settings')->insert($setting + [
                    'group' => 'footer',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'footer')->delete();
        Cache::forget('settings.all');
    }
};
