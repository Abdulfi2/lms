<?php

namespace Tests\Unit;

use App\Support\RichText;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_legacy_plain_text_keeps_line_breaks_and_is_escaped(): void
    {
        $out = RichText::render("Baris 1\nBaris <2> & 3");

        $this->assertSame("Baris 1<br />\nBaris &lt;2&gt; &amp; 3", $out);
    }

    public function test_html_formatting_is_kept(): void
    {
        $html = '<h2>Judul</h2><p>Isi <strong>tebal</strong> dan <a href="https://zakatsukses.org">tautan</a></p><ul><li>satu</li></ul>';

        $this->assertSame($html, RichText::render($html));
    }

    public function test_dangerous_markup_is_removed(): void
    {
        $out = RichText::render('<p onclick="x()">Halo</p><script>alert(1)</script><iframe src="//e.com"></iframe>'
            . '<img src="a.png" onerror="alert(1)"><a href="javascript:alert(1)">klik</a>');

        $this->assertStringNotContainsString('<script', $out);
        $this->assertStringNotContainsString('<iframe', $out);
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringNotContainsString('onerror', $out);
        $this->assertStringNotContainsString('javascript:', $out);
        $this->assertStringContainsString('<p>Halo</p>', $out);
        $this->assertStringContainsString('<img src="a.png">', $out);
    }

    public function test_empty_and_null(): void
    {
        $this->assertSame('', RichText::render(null));
        $this->assertSame('', RichText::render(''));
    }

    public function test_excerpt_strips_tags_and_limits(): void
    {
        $this->assertSame('Halo dunia & kawan', RichText::excerpt('<p>Halo   <strong>dunia</strong> &amp; kawan</p>'));
        $this->assertSame('abcde...', RichText::excerpt('<p>abcdefghij</p>', 5));
    }
}
