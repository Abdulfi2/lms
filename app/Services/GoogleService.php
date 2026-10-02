<?php

namespace App\Services;

use App\Models\TokenDetails;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message as GmailMessage;
use Google\Service\Oauth2;

class GoogleService
{
    protected const TOKEN_KEY = 'GmailAPI';

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Gmail::GMAIL_SEND);
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function redirectToGoogle()
    {
        return redirect($this->client->createAuthUrl());
    }

    /**
     * Tukar authorization code dengan access/refresh token, simpan ke DB,
     * dan kembalikan alamat email akun Google yang baru saja mengotorisasi.
     */
    public function handleGoogleCallback(string $code): string
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException('Google OAuth error: ' . ($token['error_description'] ?? $token['error']));
        }

        $this->client->setAccessToken($token);

        $oauth2 = new Oauth2($this->client);
        $email = $oauth2->userinfo->get()->getEmail();

        $expiresAt = Carbon::now()->addSeconds($token['expires_in']);

        TokenDetails::updateOrCreate(
            ['model_token' => self::TOKEN_KEY],
            [
                'email' => $email,
                'token' => $token['access_token'],
                'token_type' => $token['token_type'] ?? 'Bearer',
                'expires_in' => $expiresAt,
                'refresh_token' => $token['refresh_token'] ?? TokenDetails::where('model_token', self::TOKEN_KEY)->value('refresh_token'),
                'scope' => $token['scope'] ?? null,
            ]
        );

        return $email;
    }

    public function isAuthorized(): bool
    {
        return TokenDetails::where('model_token', self::TOKEN_KEY)->whereNotNull('refresh_token')->exists();
    }

    public function authorizedEmail(): ?string
    {
        return TokenDetails::where('model_token', self::TOKEN_KEY)->value('email');
    }

    public function disconnect(): void
    {
        TokenDetails::where('model_token', self::TOKEN_KEY)->delete();
    }

    /**
     * Pastikan client punya access token yang masih valid (refresh kalau sudah kedaluwarsa).
     */
    protected function ensureValidAccessToken(): string
    {
        $tokenData = TokenDetails::where('model_token', self::TOKEN_KEY)->first();

        if (!$tokenData || !$tokenData->refresh_token) {
            throw new \RuntimeException('Google belum diotorisasi. Silakan hubungkan akun Google terlebih dahulu.');
        }

        if (Carbon::now()->greaterThanOrEqualTo($tokenData->expires_in)) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($tokenData->refresh_token);

            if (isset($newToken['error'])) {
                throw new \RuntimeException('Gagal refresh token Google: ' . ($newToken['error_description'] ?? $newToken['error']));
            }

            $tokenData->update([
                'token' => $newToken['access_token'],
                'expires_in' => Carbon::now()->addSeconds($newToken['expires_in']),
                'refresh_token' => $newToken['refresh_token'] ?? $tokenData->refresh_token,
            ]);

            return $newToken['access_token'];
        }

        return $tokenData->token;
    }

    /**
     * Kirim email mentah (format RFC 2822, belum di-base64url-encode) lewat Gmail API.
     */
    public function sendRawMessage(string $rfc2822Message): void
    {
        $this->client->setAccessToken($this->ensureValidAccessToken());

        $gmail = new Gmail($this->client);

        $encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rfc2822Message));

        $message = new GmailMessage();
        $message->setRaw($encoded);

        $gmail->users_messages->send('me', $message);
    }
}
