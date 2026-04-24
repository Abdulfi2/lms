<?php
// app/Jobs/SendAssignmentNotificationJob.php

namespace App\Jobs;

use App\Models\Assignment;
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
            $student->notify(new NewAssignmentNotification($this->assignment));
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Gagal kirim notifikasi assignment: ' . $this->assignment->id, [
            'error' => $exception->getMessage()
        ]);
    }
}