<?php

namespace App\Support;

/**
 * Daftar field yang bisa diatur admin untuk template sertifikat kelulusan
 * (resources/views/certificates/template.blade.php). Layout/posisi elemen tetap
 * ditentukan oleh CSS di template - di sini hanya konten (teks + gambar) yang
 * bisa diganti tanpa menyentuh kode.
 */
class CertificateSettings
{
    public const FOLDER = 'certificates/template';

    public static function defaults(): array
    {
        return [
            ['key' => 'certificate_title', 'value' => 'SERTIFIKAT KELULUSAN', 'type' => 'text', 'label' => 'Judul Sertifikat'],
            ['key' => 'certificate_subtitle_given_to', 'value' => 'Diberikan kepada', 'type' => 'text', 'label' => 'Teks "Diberikan kepada"'],
            ['key' => 'certificate_subtitle_for_course', 'value' => 'Atas kelulusannya pada kelas', 'type' => 'text', 'label' => 'Teks "Atas kelulusannya pada kelas"'],
            ['key' => 'certificate_signature_name', 'value' => 'Dr. Sunarto Zulkifli S.TP, MM', 'type' => 'text', 'label' => 'Nama Penanda Tangan'],
            ['key' => 'certificate_signature_title', 'value' => 'Direktur', 'type' => 'text', 'label' => 'Jabatan Penanda Tangan'],
            ['key' => 'certificate_logo', 'value' => '', 'type' => 'image', 'label' => 'Logo (pojok kiri atas)'],
            ['key' => 'certificate_background', 'value' => '', 'type' => 'image', 'label' => 'Latar Belakang Sertifikat'],
            ['key' => 'certificate_signature_image', 'value' => '', 'type' => 'image', 'label' => 'Gambar Tanda Tangan (opsional)'],
        ];
    }

    public static function hints(): array
    {
        return [
            'certificate_logo' => 'Kosongkan untuk pakai logo bawaan sistem. Disarankan PNG transparan.',
            'certificate_background' => 'Kosongkan untuk pakai latar bawaan sistem. Ukuran disarankan A4 landscape (1123x794px).',
            'certificate_signature_image' => 'Opsional - gambar tanda tangan/cap yang ditampilkan di atas nama penanda tangan.',
        ];
    }
}
