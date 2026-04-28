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
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

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
        try {
            Log::info('GenerateCertificateJob STARTED', [
                'enrollment_id' => $this->enrollment->id ?? null
            ]);

            // Load relations
            $this->enrollment->load(['user', 'course']);

            $user = $this->enrollment->user;
            $course = $this->enrollment->course;

            if (!$user || !$course) {
                Log::error('Certificate generation failed: User or Course not found');
                return;
            }

            // Cek apakah sudah ada sertifikat
            if (Certificate::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                Log::info('Certificate already exists');
                return;
            }

            $certificateNumber = 'CERT-' . strtoupper(uniqid()) . '-' . $user->id . '-' . $course->id;
            $verificationCode = md5($certificateNumber . $user->email . now());
            $verificationUrl = url('/certificate/verify/' . $verificationCode);

            // ========== GENERATE QR CODE (v5.x) ==========
            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($verificationUrl)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(200)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin) // menggunakan enum langsung
                ->build();

            $qrCodeBase64 = base64_encode($qrCode->getString());

            $data = [
                'user' => $user,
                'course' => $course,
                'issued_at' => now()->format('d F Y'),
                'certificate_number' => $certificateNumber,
                'verification_code' => $verificationCode,
                'verification_url' => $verificationUrl,
                'qr_code' => $qrCodeBase64,
                'sections' => $course->sections,
                'total_hours' => $course->duration_total ?? 45,
            ];

            // Generate PDF
            $pdf = Pdf::loadView('certificates.template', $data);
            $pdf->setPaper('A4', 'landscape');

            $filename = 'certificates/certificate_' . $user->id . '_' . $course->id . '.pdf';

            if (!Storage::disk('public')->exists('certificates')) {
                Storage::disk('public')->makeDirectory('certificates');
            }

            Storage::disk('public')->put($filename, $pdf->output());

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => $certificateNumber,
                'url' => Storage::url($filename),
                'file_path' => $filename,
                'issued_at' => now(),
                'is_verified' => true,
                'verification_code' => $verificationCode,
            ]);

            $this->enrollment->update(['certificate_issued_at' => now()]);

            Log::info('Certificate generated successfully');

        } catch (\Exception $e) {
            Log::error('Certificate generation failed: ' . $e->getMessage(), [
                'enrollment_id' => $this->enrollment->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}