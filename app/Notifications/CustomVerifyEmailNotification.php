<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmailNotification extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email - ' . config('app.name'))
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di ' . config('app.name') . '. Klik tombol di bawah untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Email', $url)
            ->line('Link verifikasi ini akan kedaluwarsa dalam ' . config('auth.verification.expire', 60) . ' menit.')
            ->line('Jika Anda tidak merasa membuat akun ini, abaikan email ini.')
            ->salutation('Salam, ' . config('app.name') . ' Team');
    }
}
