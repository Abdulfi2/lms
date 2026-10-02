<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_clicking_verification_link_while_logged_out_redirects_to_login_instead_of_crashing(): void
    {
        // Reproduksi bug produksi: route verification.verify sempat hanya
        // dilindungi middleware 'signed', tanpa 'auth' — jadi Auth::user()
        // null saat link dibuka di browser/sesi yang belum login, dan
        // EmailVerificationRequest::authorize() crash manggil getKey() di null.
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Tanpa actingAs() — benar-benar belum login, sama seperti klik link dari email.
        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('login'));
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_can_login_and_complete_verification_after_clicking_link_while_logged_out(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 1. Buka link verifikasi tanpa login -> diarahkan ke login, URL tujuan disimpan sebagai "intended".
        $this->get($verificationUrl)->assertRedirect(route('login'));

        // 2. Login dengan kredensial benar -> TIDAK boleh langsung di-logout paksa
        //    hanya karena belum verifikasi (itu akar masalah kedua dari bug ini),
        //    dan harus diarahkan balik ke link verifikasi yang tadi dituju.
        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $loginResponse->assertRedirect($verificationUrl);

        // 3. Mengikuti redirect tadi akhirnya benar-benar memverifikasi email.
        $this->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
