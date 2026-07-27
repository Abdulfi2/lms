<?php
// app/Jobs/SendAssignmentNotificationJob.php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAssignmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 180, 600];

    protected $assignment;
    protected $students;

    public function __construct(Assignment $assignment, $students)
    {
        $this->assignment = $assignment;
        $this->students = $students;
    }

    public function handle(): void
    {
        foreach ($this->students as $student) {
            // Catat notifikasi in-app dulu, terpisah dari pengiriman email — supaya
            // gangguan pada mail server tidak menghalangi notifikasi in-app muncul.
            Notification::create([
                'user_id' => $student->id,
                'type' => 'new_assignment',
                'title' => 'Tugas Baru: ' . $this->assignment->title,
                'message' => 'Tugas baru telah ditambahkan pada kursus "' . $this->assignment->course->title . '".',
                'action_url' => '/student/assignments/' . $this->assignment->id,
                'channel' => 'database',
                'sent_at' => now(),
            ]);

            try {
                $student->notify(new NewAssignmentNotification($this->assignment));
            } catch (\Throwable $e) {
                Log::error('Gagal kirim email notifikasi assignment ke user ' . $student->id . ': ' . $e->getMessage());
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Gagal kirim notifikasi assignment: ' . $this->assignment->id, [
            'error' => $exception->getMessage()
        ]);
    }
}