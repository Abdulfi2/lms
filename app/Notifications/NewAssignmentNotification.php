<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAssignmentNotification extends Notification
{
    use Queueable;

    public function __construct(protected Assignment $assignment)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tugas Baru: ' . $this->assignment->title)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Tugas baru telah ditambahkan pada kursus "' . $this->assignment->course->title . '".')
            ->line('Judul Tugas: ' . $this->assignment->title)
            ->when($this->assignment->due_date, fn ($mail) => $mail->line('Batas Waktu: ' . $this->assignment->due_date->format('d M Y H:i')))
            ->action('Lihat Tugas', url('/student/assignments/' . $this->assignment->id))
            ->salutation('Salam, ' . config('app.name') . ' Team');
    }
}
