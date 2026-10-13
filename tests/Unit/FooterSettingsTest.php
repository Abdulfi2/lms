<?php

namespace Tests\Unit;

use App\Support\FooterSettings;
use PHPUnit\Framework\TestCase;

class FooterSettingsTest extends TestCase
{
    public function test_safe_urls(): void
    {
        foreach (['/faq', '#top', 'https://zakatsukses.org/x?a=1', 'http://a.co', 'mailto:info@zakatsukses.org', 'tel:+62211234'] as $url) {
            $this->assertTrue(FooterSettings::isSafeUrl($url), $url);
        }
    }

    public function test_unsafe_urls(): void
    {
        foreach (['', 'javascript:alert(1)', 'JaVaScRiPt:alert(1)', 'data:text/html,x', '//evil.com', 'ftp://x.com', 'https://', '/a b', "/a\nb", 'vbscript:x'] as $url) {
            $this->assertFalse(FooterSettings::isSafeUrl($url), $url);
        }
    }

    public function test_parse_links_skips_broken_and_unsafe_lines(): void
    {
        $text = "Tentang Kami | /about\n\n  Blog|https://blog.example.org \nTanpa url\n| /kosong\nJahat | javascript:alert(1)\r\nKontak | /contact";

        $this->assertSame([
            ['label' => 'Tentang Kami', 'url' => '/about'],
            ['label' => 'Blog', 'url' => 'https://blog.example.org'],
            ['label' => 'Kontak', 'url' => '/contact'],
        ], FooterSettings::parseLinks($text));
    }

    public function test_url_may_contain_pipe_in_query(): void
    {
        $this->assertSame(
            [['label' => 'Cari', 'url' => '/search?q=a|b']],
            FooterSettings::parseLinks('Cari | /search?q=a|b')
        );
    }

    public function test_invalid_lines_report_line_numbers(): void
    {
        $problems = FooterSettings::invalidLines("OK | /ok\n\nTanpa url\nJahat | javascript:alert(1)");

        $this->assertCount(2, $problems);
        $this->assertStringContainsString('baris 3', $problems[0]);
        $this->assertStringContainsString('baris 4', $problems[1]);
        $this->assertSame([], FooterSettings::invalidLines(''));
    }

    public function test_social_url_must_be_http(): void
    {
        $this->assertTrue(FooterSettings::isSocialUrl('https://instagram.com/zakatsukses'));
        $this->assertFalse(FooterSettings::isSocialUrl('javascript:alert(1)'));
        $this->assertFalse(FooterSettings::isSocialUrl('@zakatsukses'));
        $this->assertFalse(FooterSettings::isSocialUrl(null));
    }

    public function test_defaults_cover_every_social_key(): void
    {
        $keys = array_column(FooterSettings::defaults(), 'key');

        foreach (array_keys(FooterSettings::SOCIALS) as $key) {
            $this->assertContains($key, $keys);
        }
        $this->assertSame(count($keys), count(array_unique($keys)));
    }
}
