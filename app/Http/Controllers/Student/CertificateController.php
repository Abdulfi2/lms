<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificateJob;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Daftar sertifikat milik user yang sedang login.
     */
    public function index()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->orderBy('issued_at', 'desc')
            ->paginate(12);

        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Preview sertifikat (menampilkan PDF di iframe).
     */
    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('student.certificates.show', compact('certificate'));
    }

    /**
     * Download file PDF sertifikat.
     */
    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cari path file
        $filePath = $certificate->file_path;
        if (!$filePath) {
            // Ambil dari URL jika file_path kosong
            $filePath = str_replace('/storage/', '', $certificate->url);
        }

        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        $fileName = 'sertifikat_' . $certificate->certificate_number . '.pdf';

        return response()->download($fullPath, $fileName);
    }

    /**
     * Halaman khusus untuk mencetak sertifikat (tanpa layout LMS).
     */
    public function print(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $filePath = $certificate->file_path;
        if (!$filePath) {
            $filePath = str_replace('/storage/', '', $certificate->url);
        }

        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        $pdfUrl = Storage::url($filePath);

        return view('student.certificates.print', compact('certificate', 'pdfUrl'));
    }

    /**
     * Verifikasi sertifikat publik (tanpa login) berdasarkan kode verifikasi.
     */
    public function verify($code)
    {
        $certificate = Certificate::where('verification_code', $code)
            ->with(['user', 'course'])
            ->firstOrFail();

        return view('public.certificate.verify', compact('certificate'));
    }

    /**
     * Generate ulang sertifikat untuk course tertentu.
     */
    public function regenerate($slug)
    {
        $user = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        // Cek apakah user terdaftar di course ini
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.my-courses')
                ->with('error', 'Anda tidak terdaftar di kursus ini.');
        }

        // Cek apakah course sudah selesai (progress 100%)
        if ($enrollment->progress < 100) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Anda harus menyelesaikan kursus terlebih dahulu untuk mendapatkan sertifikat.');
        }

        // Hapus sertifikat lama jika ada
        $oldCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($oldCertificate) {
            // Hapus file lama
            if ($oldCertificate->file_path && Storage::disk('public')->exists($oldCertificate->file_path)) {
                Storage::disk('public')->delete($oldCertificate->file_path);
            }
            $oldCertificate->delete();
        }

        // Generate sertifikat baru
        try {
            GenerateCertificateJob::dispatchSync($enrollment);

            // Ambil sertifikat yang baru dibuat
            $certificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($certificate) {
                return redirect()->route('student.certificates.show', $certificate)
                    ->with('success', 'Sertifikat berhasil digenerate. Selamat! 🎉');
            } else {
                return redirect()->route('student.courses.show', $course->slug)
                    ->with('warning', 'Sertifikat sedang diproses. Silakan cek kembali dalam beberapa saat.');
            }

        } catch (\Exception $e) {
            \Log::error('Certificate regeneration failed: ' . $e->getMessage());
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Gagal mengenerate sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Cek status sertifikat (AJAX).
     */
    public function checkStatus(Course $course)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || $enrollment->progress < 100) {
            return response()->json([
                'available' => false,
                'message' => 'Selesaikan kursus terlebih dahulu.'
            ]);
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return response()->json([
            'available' => !is_null($certificate),
            'certificate_id' => $certificate->id ?? null,
            'certificate_url' => $certificate ? route('student.certificates.show', $certificate) : null,
            'message' => $certificate ? 'Sertifikat tersedia' : 'Sertifikat sedang diproses'
        ]);
    }
}