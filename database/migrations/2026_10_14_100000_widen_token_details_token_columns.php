<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * token_details.token/refresh_token di-set VARCHAR(255) sejak awal, tapi access token
     * OAuth2 Google sekarang rutin lebih panjang dari itu (contoh nyata di produksi: token
     * "ya29...." ~260+ karakter) - menyebabkan refresh token di GoogleService::ensureValidAccessToken()
     * gagal dengan "Data too long for column 'token'" setiap kali token kedaluwarsa dan perlu
     * di-refresh (mis. saat mengirim email verifikasi registrasi lewat Gmail API).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE token_details MODIFY COLUMN token TEXT NULL');
            DB::statement('ALTER TABLE token_details MODIFY COLUMN refresh_token TEXT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE token_details MODIFY COLUMN token VARCHAR(255) NULL');
            DB::statement('ALTER TABLE token_details MODIFY COLUMN refresh_token VARCHAR(255) NULL');
        }
    }
};
