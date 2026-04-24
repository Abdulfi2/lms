<?php
// app/Listeners/LogFailedJob.php

namespace App\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

class LogFailedJob
{
    public function handle(JobFailed $event): void
    {
        // Kirim notifikasi ke admin via email/telegram
        Log::channel('slack')->critical('Job Failed!', [
            'job' => $event->job->resolveName(),
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'exception' => $event->exception->getMessage()
        ]);
    }
}