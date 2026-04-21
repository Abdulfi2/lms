<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Create a new password reset token
     */
    public function createToken(string $email): string
    {
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
                'expires_at' => now()->addMinutes(config('auth.passwords.users.expire', 60))
            ]
        );

        return $token;
    }

    /**
     * Check if token is valid and not expired
     */
    public function isValidToken(string $email, string $token): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return false;
        }

        // Cek apakah token sudah expired
        if ($record->expires_at && now()->gt($record->expires_at)) {
            return false;
        }

        // Cek apakah created_at + expire sudah lewat (fallback)
        $expiresAt = $record->expires_at ?? $record->created_at->addMinutes(config('auth.passwords.users.expire', 60));
        if (now()->gt($expiresAt)) {
            return false;
        }

        return Hash::check($token, $record->token);
    }

    /**
     * Delete expired tokens (cleanup)
     */
    public function deleteExpiredTokens(): int
    {
        return DB::table('password_reset_tokens')
            ->where('expires_at', '<', now())
            ->orWhere('created_at', '<', now()->subMinutes(config('auth.passwords.users.expire', 60)))
            ->delete();
    }
}