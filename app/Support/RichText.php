<?php

namespace App\Support;

/**
 * Menampilkan isian yang dulu berupa teks biasa dan sekarang disunting lewat TinyMCE (HTML).
 *
 * - Data lama (teks polos, tanpa tag) tetap tampil seperti sebelumnya: di-escape lalu baris barunya
 *   menjadi <br>.
 * - Data baru (HTML) hanya diizinkan memakai tag formatting yang aman; <script>, <iframe>, <style>,
 *   atribut on*="..." dan URL javascript: dibuang.
 *
 * Ini lapisan pertahanan tambahan, bukan sanitizer penuh seperti HTMLPurifier. Pemakaiannya wajar
 * selama yang menulis adalah staf tepercaya (admin, event manager, support). Kalau suatu saat
 * isiannya bisa ditulis pengguna umum, ganti dengan HTMLPurifier.
 */
class RichText
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><span><div><h1><h2><h3><h4><h5><h6>'
        . '<ul><ol><li><a><blockquote><pre><code><hr><table><thead><tbody><tr><th><td><img>';

    public static function render(?string $value): string
    {
        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        if (!self::looksLikeHtml($value)) {
            return nl2br(e($value));
        }

        $html = strip_tags($value, self::ALLOWED_TAGS);

        // Atribut event handler (onclick, onerror, ...) dan style/srcdoc.
        $html = preg_replace('/\s+(on[a-z]+|style|srcdoc)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        // URL berskema javascript:/data:/vbscript: pada href dan src.
        $html = preg_replace(
            '/\b(href|src)\s*=\s*(["\']?)\s*(?:javascript|vbscript|data)\s*:[^"\'>\s]*\2/i',
            '$1="#"',
            $html
        );

        return $html;
    }

    /** Ringkasan satu baris tanpa tag, untuk daftar/kartu. */
    public static function excerpt(?string $value, int $limit = 100): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        return \Illuminate\Support\Str::limit($text, $limit);
    }

    private static function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\s*\/?\s*[a-z][a-z0-9]*[^>]*>/i', $value);
    }
}
