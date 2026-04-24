<?php
// app/Jobs/SendVerificationEmailJob.php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Auth\Notifications\VerifyEmail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3; // Maksimal 3 percobaan
    public $backoff = [60, 300, 600]; // Delay: 1 menit, 5 menit, 10 menit

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $this->user->notify(new VerifyEmail);
    }

    public function failed(\Throwable $exception): void
    {
        // Log failure ke database atau sentry
        \Log::error('Email verifikasi gagal dikirim ke: ' . $this->user->email, [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage()
        ]);
    }
}