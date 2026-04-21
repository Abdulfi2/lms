<?php

namespace App\Services;

use App\Models\TokenDetails;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Gmail;
use Illuminate\Http\Request;

class GoogleService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope(Gmail::GMAIL_SEND);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    public function redirectToGoogle()
    {
        return redirect($this->client->createAuthUrl());
    }

    public function handleGoogleCallback($code)
    {
        $this->client->authenticate($code);
        $token = $this->client->getAccessToken();

        // Hitung kapan token akan kedaluwarsa
        $expiresAt = Carbon::now()->addSeconds($token['expires_in']);

        // Cek apakah token sudah ada di database
        $existingToken = TokenDetails::where('model_token', 'GmailAPI')->first();

        if ($existingToken) {
            // Update token yang ada
            $existingToken->update([
                'model_token' => 'GmailAPI',
                'token' => $token['access_token'],
                'token_type' => 'Bearer',
                'expires_in' => $expiresAt->toDateTimeString(),
                'refresh_token' => $token['refresh_token'] ?? $existingToken->refresh_token,
                'scope' => $token['scope'],
            ]);
        } else {
            // Simpan token baru ke database
            TokenDetails::create([
                'model_token' => 'GmailAPI',
                'token' => $token['access_token'],
                'token_type' => 'Bearer',
                'expires_in' => $expiresAt->toDateTimeString(),
                'refresh_token' => $token['refresh_token'] ?? null,
                'scope' => $token['scope'],
            ]);
        }

        return redirect('/')->with('success', 'Google Authentication Successful!');
    }

    public function refreshAccessToken()
    {
        // Ambil token dari database
        $tokenData = TokenDetails::where('model_token', 'GmailAPI')->first();

        if (!$tokenData || !$tokenData->refresh_token) {
            throw new \Exception("Refresh token is missing or not available.");
        }

        // Gunakan refresh token untuk mendapatkan access token baru
        $newToken = $this->client->fetchAccessTokenWithRefreshToken($tokenData->refresh_token);

        // Periksa apakah ada error saat refresh
        if (isset($newToken['error'])) {
            throw new \Exception("Error refreshing token: " . $newToken['error_description']);
        }

        // Perbarui waktu kedaluwarsa token
        $expiresAt = Carbon::now()->addSeconds($newToken['expires_in']);

        // Simpan token baru ke database
        $tokenData->update([
            'token' => $newToken['access_token'],
            'expires_in' => $expiresAt->toDateTimeString(),
            'refresh_token' => $newToken['refresh_token'] ?? $tokenData->refresh_token,
        ]);

        return $newToken['access_token'];
    }

    public function sendEmail($to, $subject, $template, $data = [])
    {
        // Ambil token dari database
        $tokenData = TokenDetails::where('model_token', 'GmailAPI')->first();

        if (!$tokenData) {
            throw new \Exception("Google authentication required.");
        }

        // Cek apakah token sudah expired
        $expiresAt = Carbon::parse($tokenData->expires_in);

        if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
            // Jika expired, refresh token
            $newAccessToken = $this->refreshAccessToken();
        } else {
            $newAccessToken = $tokenData->token;
        }

        // Set token baru ke Google Client
        $this->client->setAccessToken($newAccessToken);

        // Render Blade template ke HTML string
        $htmlMessage = view('emails.' . $template, $data)->render();

        // Kirim email melalui Gmail API
        $gmail = new Gmail($this->client);

        $emailMessage = "To: $to\r\n";
        $emailMessage .= "Subject: $subject\r\n";
        $emailMessage .= "MIME-Version: 1.0\r\n";
        $emailMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $emailMessage .= $htmlMessage;

        $rawMessage = base64_encode($emailMessage);
        $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

        $message = new \Google\Service\Gmail\Message();
        $message->setRaw($rawMessage);

        return $gmail->users_messages->send('me', $message);
    }


    // public function sendEmailCreateOrUpdateToken($to, $message)
    // {
    //     // Ambil token dari database
    //     $tokenData = TokenDetails::where('model_token', 'GmailAPI')->first();

    //     if (!$tokenData) {
    //         throw new \Exception("Google authentication required.");
    //     }

    //     // Cek apakah token sudah expired
    //     $expiresAt = Carbon::parse($tokenData->expires_in);

    //     if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
    //         // Jika expired, refresh token
    //         $subject = 'Token Berhasil Direfresh';
    //         $newAccessToken = $this->refreshAccessToken();
    //     } else {
    //         // Jika masih valid, gunakan token yang ada
    //         $subject = 'Token Berhasil Dibuat';
    //         $newAccessToken = $tokenData->token;
    //     }

    //     // Set token baru ke Google Client
    //     $this->client->setAccessToken($newAccessToken);

    //     // Kirim email melalui Gmail API
    //     $gmail = new Gmail($this->client);

    //     $emailMessage = "To: $to\r\n";
    //     $emailMessage .= "Subject: $subject\r\n";
    //     $emailMessage .= "MIME-Version: 1.0\r\n";
    //     $emailMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    //     $emailMessage .= $message;

    //     $rawMessage = base64_encode($emailMessage);
    //     $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

    //     $message = new \Google\Service\Gmail\Message();
    //     $message->setRaw($rawMessage);

    //     return $gmail->users_messages->send('me', $message);
    // }
}