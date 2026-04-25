<?php
// app/Jobs/GenerateCertificateJob.php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Enrollment;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function handle()
    {
        $user = $this->enrollment->user;
        $course = $this->enrollment->course;

        // Cek apakah sudah ada sertifikat
        if (Certificate::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return;
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'date' => now(),
            'certificate_number' => 'CERT-' . strtoupper(uniqid()),
        ];

        $pdf = Pdf::loadView('certificates.template', $data);
        $filename = 'certificates/certificate_' . $user->id . '_' . $course->id . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $data['certificate_number'],
            'url' => Storage::url($filename),
            'file_path' => $filename,
            'issued_at' => now(),
            'is_verified' => true,
            'verification_code' => md5(uniqid()),
        ]);
    }
}