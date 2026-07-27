<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => config('app.name', 'LMS'), 'type' => 'text', 'label' => 'Nama Situs'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Belajar Tanpa Batas', 'type' => 'text', 'label' => 'Tagline Situs'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'support@lms.com', 'type' => 'text', 'label' => 'Email Kontak'],
            ['group' => 'general', 'key' => 'contact_phone', 'value' => '', 'type' => 'text', 'label' => 'Nomor Telepon/WhatsApp'],

            // Email
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => config('app.name', 'LMS'), 'type' => 'text', 'label' => 'Nama Pengirim Email'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => 'noreply@lms.com', 'type' => 'text', 'label' => 'Alamat Email Pengirim'],
            ['group' => 'email', 'key' => 'mail_footer_text', 'value' => 'Email ini dikirim otomatis, mohon tidak membalas.', 'type' => 'textarea', 'label' => 'Teks Footer Email'],

            // Payment Gateway (penyimpanan konfigurasi saja, belum ada integrasi charging nyata)
            ['group' => 'payment', 'key' => 'payment_gateway_provider', 'value' => 'none', 'type' => 'text', 'label' => 'Provider Payment Gateway'],
            ['group' => 'payment', 'key' => 'payment_gateway_api_key', 'value' => '', 'type' => 'text', 'label' => 'API Key / Client Key'],
            ['group' => 'payment', 'key' => 'payment_gateway_secret_key', 'value' => '', 'type' => 'text', 'label' => 'Secret Key'],
            ['group' => 'payment', 'key' => 'payment_gateway_sandbox', 'value' => '1', 'type' => 'boolean', 'label' => 'Mode Sandbox/Testing'],

            // Homepage
            ['group' => 'homepage', 'key' => 'hero_title', 'value' => 'Belajar Tanpa Batas', 'type' => 'text', 'label' => 'Judul Hero'],
            ['group' => 'homepage', 'key' => 'hero_subtitle', 'value' => 'Tingkatkan Skillmu Sekarang!', 'type' => 'text', 'label' => 'Sub-judul Hero'],
            ['group' => 'homepage', 'key' => 'hero_description', 'value' => 'Platform pembelajaran online terbaik dengan ribuan kursus berkualitas dari instruktur berpengalaman. Mulai perjalanan belajarmu hari ini!', 'type' => 'textarea', 'label' => 'Deskripsi Hero'],
            ['group' => 'homepage', 'key' => 'stat_students', 'value' => '50K+', 'type' => 'text', 'label' => 'Statistik: Siswa Aktif'],
            ['group' => 'homepage', 'key' => 'stat_courses', 'value' => '500+', 'type' => 'text', 'label' => 'Statistik: Kursus Premium'],
            ['group' => 'homepage', 'key' => 'stat_instructors', 'value' => '200+', 'type' => 'text', 'label' => 'Statistik: Instruktur Ahli'],
            ['group' => 'homepage', 'key' => 'stat_certificates', 'value' => '100K+', 'type' => 'text', 'label' => 'Statistik: Sertifikat Diterbitkan'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
