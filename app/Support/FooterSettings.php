<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Pengaturan footer homepage (grup 'footer' di tabel settings).
 *
 * Daftar tautan disimpan sebagai teks, satu tautan per baris dengan format "Label | URL", supaya
 * bisa diedit lewat form pengaturan generik tanpa tabel baru.
 */
class FooterSettings
{
    public const SOCIALS = [
        'footer_facebook' => ['label' => 'Facebook', 'icon' => 'fab fa-facebook-f'],
        'footer_twitter' => ['label' => 'X / Twitter', 'icon' => 'fab fa-twitter'],
        'footer_instagram' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram'],
        'footer_linkedin' => ['label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in'],
        'footer_youtube' => ['label' => 'YouTube', 'icon' => 'fab fa-youtube'],
    ];

    /** Nilai awal; dipakai migrasi dan seeder. Default meniru footer sebelum bisa diatur. */
    public static function defaults(): array
    {
        return [
            ['key' => 'footer_description', 'value' => 'Platform pembelajaran online milik LAZ Zakat Sukses.', 'type' => 'textarea', 'label' => 'Deskripsi Singkat'],
            ['key' => 'footer_about_title', 'value' => 'Tentang', 'type' => 'text', 'label' => 'Judul Kolom Tautan 1'],
            ['key' => 'footer_about_links', 'value' => "Tentang Kami | /about\nBlog | /articles", 'type' => 'textarea', 'label' => 'Tautan Kolom 1'],
            ['key' => 'footer_help_title', 'value' => 'Bantuan', 'type' => 'text', 'label' => 'Judul Kolom Tautan 2'],
            ['key' => 'footer_help_links', 'value' => "FAQ | /faq\nKontak | /contact\nKebijakan Privasi | /privacy-policy\nSyarat & Ketentuan | /terms", 'type' => 'textarea', 'label' => 'Tautan Kolom 2'],
            ['key' => 'footer_phone', 'value' => '', 'type' => 'text', 'label' => 'Nomor Telepon'],
            ['key' => 'footer_address', 'value' => '', 'type' => 'textarea', 'label' => 'Alamat'],
            ['key' => 'footer_facebook', 'value' => '', 'type' => 'text', 'label' => 'Tautan Facebook'],
            ['key' => 'footer_twitter', 'value' => '', 'type' => 'text', 'label' => 'Tautan X / Twitter'],
            ['key' => 'footer_instagram', 'value' => '', 'type' => 'text', 'label' => 'Tautan Instagram'],
            ['key' => 'footer_linkedin', 'value' => '', 'type' => 'text', 'label' => 'Tautan LinkedIn'],
            ['key' => 'footer_youtube', 'value' => '', 'type' => 'text', 'label' => 'Tautan YouTube'],
            ['key' => 'footer_copyright', 'value' => '© {year} ZS Academy. All rights reserved.', 'type' => 'text', 'label' => 'Teks Hak Cipta'],
        ];
    }

    /**
     * Nilai satu pengaturan footer. Kalau barisnya belum ada di database (migrasi belum dijalankan)
     * dipakai nilai default; kalau barisnya ada tapi dikosongkan admin, kosong itu yang dihormati.
     */
    public static function value(string $key): ?string
    {
        $default = collect(self::defaults())->firstWhere('key', $key)['value'] ?? null;

        return Setting::get($key, $default);
    }

    /**
     * Petunjuk di bawah tiap field pada halaman pengaturan.
     */
    public static function hints(): array
    {
        $links = 'Satu tautan per baris, format: Label | URL. URL boleh alamat lengkap (https://...) atau path di situs ini (/faq).';

        $hints = [
            'footer_about_links' => $links,
            'footer_help_links' => $links,
            'footer_phone' => 'Kosongkan untuk menyembunyikan. Email kontak diatur di tab Umum (Email Kontak).',
            'footer_address' => 'Kosongkan untuk menyembunyikan.',
            'footer_copyright' => 'Gunakan {year} untuk tahun otomatis.',
        ];

        foreach (self::SOCIALS as $key => $social) {
            $hints[$key] = 'Alamat lengkap (https://...). Kosongkan untuk menyembunyikan ikon ' . $social['label'] . '.';
        }

        return $hints;
    }

    /**
     * Urai teks "Label | URL" menjadi daftar tautan yang aman ditampilkan. Baris rusak dilewati.
     *
     * @return array<int, array{label: string, url: string}>
     */
    public static function parseLinks(?string $text): array
    {
        $links = [];

        foreach (self::lines($text) as $line) {
            [$label, $url] = self::splitLine($line);

            if ($label !== '' && self::isSafeUrl($url)) {
                $links[] = ['label' => $label, 'url' => $url];
            }
        }

        return $links;
    }

    /**
     * Baris yang tidak valid (nomor baris berbasis 1 + alasannya) untuk pesan error di form.
     *
     * @return string[]
     */
    public static function invalidLines(?string $text): array
    {
        $problems = [];

        foreach (self::lines($text) as $number => $line) {
            [$label, $url] = self::splitLine($line);

            if ($label === '' || $url === '') {
                $problems[] = 'baris ' . ($number + 1) . ' harus berformat "Label | URL"';
            } elseif (!self::isSafeUrl($url)) {
                $problems[] = 'baris ' . ($number + 1) . ' memakai URL yang tidak diizinkan (gunakan https://, /path, mailto:, atau tel:)';
            }
        }

        return $problems;
    }

    /** Hanya http(s), mailto, tel, path relatif ("/...") dan anchor ("#..."). Menolak javascript:, data:, dsb. */
    public static function isSafeUrl(string $url): bool
    {
        if ($url === '' || preg_match('/[\x00-\x20]/', $url)) {
            return false;
        }

        if (preg_match('/^(\/(?!\/)|#)/', $url)) {
            return true;
        }

        return (bool) preg_match('/^(https?:\/\/[^\/\s]+|mailto:[^\s@]+@[^\s@]+|tel:\+?[0-9\-]+)/i', $url);
    }

    /** Hanya http(s) yang masuk akal untuk ikon sosial. */
    public static function isSocialUrl(?string $url): bool
    {
        return (bool) preg_match('/^https?:\/\/[^\/\s]+/i', (string) $url);
    }

    public static function socials(): array
    {
        $items = [];

        foreach (self::SOCIALS as $key => $social) {
            $url = trim((string) self::value($key));

            if (self::isSocialUrl($url)) {
                $items[] = ['label' => $social['label'], 'icon' => $social['icon'], 'url' => $url];
            }
        }

        return $items;
    }

    public static function copyright(): string
    {
        $text = trim((string) self::value('footer_copyright'));

        if ($text === '') {
            $text = '© {year} ' . config('app.name', 'ZS Academy') . '. All rights reserved.';
        }

        return str_replace('{year}', date('Y'), $text);
    }

    /**
     * Baris tak kosong; kunci = nomor baris asli (berbasis 0).
     *
     * @return array<int, string>
     */
    private static function lines(?string $text): array
    {
        $lines = [];

        foreach (preg_split('/\R/u', (string) $text) as $number => $line) {
            if (trim($line) !== '') {
                $lines[$number] = trim($line);
            }
        }

        return $lines;
    }

    private static function splitLine(string $line): array
    {
        $parts = explode('|', $line, 2);

        return [trim($parts[0]), trim($parts[1] ?? '')];
    }
}
