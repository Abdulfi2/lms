<?php
// app/Jobs/GenerateCertificateJob.php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 2;
    public $backoff = [60, 300];

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function handle(): void
    {
        $user = $this->enrollment->user;
        $course = $this->enrollment->course;

        // Generate PDF Certificate
        $pdf = Pdf::loadView('certificates.template', [
            'user' => $user,
            'course' => $course,
            'date' => now()
        ]);

        // Save to storage
        $filename = 'certificates/certificate_' . $user->id . '_' . $course->id . '.pdf';
        Storage::put($filename, $pdf->output());

        // Create certificate record
        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $this->generateCertificateNumber(),
            'url' => Storage::url($filename),
            'issued_at' => now(),
            'verification_code' => uniqid()
        ]);
    }

    private function generateCertificateNumber(): string
    {
        return 'CERT-' . strtoupper(uniqid());
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Gagal generate sertifikat untuk enrollment: ' . $this->enrollment->id, [
            'error' => $exception->getMessage()
        ]);
    }
}