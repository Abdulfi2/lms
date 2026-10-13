<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FooterSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (['admin', 'instructor', 'student'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Baris pengaturan footer dibuat oleh migrasi; pastikan sudah ada di database uji.
        $this->assertTrue(Setting::where('group', 'footer')->exists(), 'Jalankan php artisan migrate untuk database uji.');
        Cache::forget('settings.all');
    }

    private function user(string $role): User
    {
        $user = User::create([
            'name' => ucfirst($role) . ' Uji',
            'email' => $role . '.' . uniqid() . '@example.test',
            'password' => bcrypt('password'),
            'default_role' => $role,
            'is_active' => true,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);

        return $user;
    }

    /** Semua field footer dengan nilai saat ini, ditimpa $override. */
    private function payload(array $override = []): array
    {
        $values = Setting::where('group', 'footer')->pluck('value', 'key')->map(fn ($v) => (string) $v)->all();

        return array_merge(['group' => 'footer'], $values, $override);
    }

    public function test_default_footer_keeps_existing_links_and_hides_empty_blocks(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach (['Tentang Kami', 'Blog', 'FAQ', 'Kontak', 'Kebijakan Privasi', 'Syarat &amp; Ketentuan'] as $label) {
            $this->assertStringContainsString($label, $html);
        }
        $this->assertStringContainsString('Platform pembelajaran online milik LAZ Zakat Sukses.', $html);
        $this->assertStringContainsString('© ' . date('Y') . ' ZS Academy. All rights reserved.', $html);
        $this->assertStringNotContainsString('fa-location-dot', $html);
        $this->assertStringNotContainsString('fa-phone', $html);
        $this->assertStringNotContainsString('fa-instagram', $html);
    }

    public function test_only_admin_can_open_footer_settings(): void
    {
        $this->actingAs($this->user('student'))
            ->get(route('admin.settings.index', ['group' => 'footer']))
            ->assertForbidden();

        $this->actingAs($this->user('admin'))
            ->get(route('admin.settings.index', ['group' => 'footer']))
            ->assertOk()
            ->assertSee('Footer')
            ->assertSee('Tautan Kolom 1')
            ->assertSee('Label | URL');
    }

    public function test_admin_changes_appear_on_homepage_footer(): void
    {
        $this->actingAs($this->user('admin'))->put(route('admin.settings.update'), $this->payload([
            'footer_description' => 'Belajar zakat dari ahlinya.',
            'footer_about_title' => 'Lembaga',
            'footer_about_links' => "Profil | /about\nLaporan | https://zakatsukses.org/laporan",
            'footer_phone' => '+62 812 0000 1111',
            'footer_address' => "Jl. Contoh No. 1\nDepok",
            'footer_instagram' => 'https://instagram.com/zakatsukses',
            'footer_copyright' => '© {year} Yayasan Zakat Sukses',
        ]))->assertRedirect(route('admin.settings.index', ['group' => 'footer']));

        auth()->logout();
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Belajar zakat dari ahlinya.', $html);
        $this->assertStringContainsString('Lembaga', $html);
        $this->assertStringContainsString('href="https://zakatsukses.org/laporan"', $html);
        $this->assertStringContainsString('+62 812 0000 1111', $html);
        $this->assertStringContainsString('Jl. Contoh No. 1<br />', $html);
        $this->assertStringContainsString('href="https://instagram.com/zakatsukses"', $html);
        $this->assertStringContainsString('© ' . date('Y') . ' Yayasan Zakat Sukses', $html);
        // Tautan eksternal dibuka di tab baru dengan rel aman.
        $this->assertMatchesRegularExpression('/href="https:\/\/zakatsukses\.org\/laporan"\s+target="_blank" rel="noopener noreferrer"/', $html);
    }

    public function test_dangerous_or_malformed_values_are_rejected_and_nothing_is_saved(): void
    {
        $before = Setting::where('key', 'footer_help_links')->value('value');

        $this->actingAs($this->user('admin'))->put(route('admin.settings.update'), $this->payload([
            'footer_help_links' => "OK | /ok\nJahat | javascript:alert(1)\nTanpa url",
            'footer_facebook' => 'javascript:alert(1)',
            'footer_instagram' => '@zakatsukses',
        ]))->assertSessionHasErrors(['footer_help_links', 'footer_facebook', 'footer_instagram']);

        $this->assertSame($before, Setting::where('key', 'footer_help_links')->value('value'));
    }

    public function test_unsafe_link_already_in_database_is_not_rendered(): void
    {
        Setting::set('footer_about_links', "Aman | /about\nJahat | javascript:alert(1)");

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Aman', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
    }

    public function test_empty_link_column_is_hidden(): void
    {
        Setting::set('footer_help_links', '');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('Syarat &amp; Ketentuan', $html);
        $this->assertStringContainsString('Tentang Kami', $html);
        $this->assertStringContainsString('md:grid-cols-3', $html);
    }

    public function test_footer_falls_back_to_defaults_when_settings_rows_are_missing(): void
    {
        // Server yang belum menjalankan migrasi footer: barisnya tidak ada sama sekali.
        Setting::where('group', 'footer')->delete();
        Cache::forget('settings.all');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Tentang Kami', $html);
        $this->assertStringContainsString('Kebijakan Privasi', $html);
        $this->assertStringContainsString('Platform pembelajaran online milik LAZ Zakat Sukses.', $html);
    }

    public function test_other_setting_groups_still_save(): void
    {
        // Baris grup 'general' berasal dari seeder, bukan migrasi, jadi dibuat sendiri agar test mandiri.
        Setting::firstOrCreate(['key' => 'contact_email'], [
            'group' => 'general', 'value' => 'lama@example.test', 'type' => 'text', 'label' => 'Email Kontak',
        ]);

        $this->actingAs($this->user('admin'))->put(route('admin.settings.update'), [
            'group' => 'general',
            'contact_email' => 'halo@zakatsukses.org',
        ])->assertRedirect();

        $this->assertSame('halo@zakatsukses.org', Setting::where('key', 'contact_email')->value('value'));
    }
}
