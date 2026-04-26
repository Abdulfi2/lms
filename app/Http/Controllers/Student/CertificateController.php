<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
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

        return view('certificates.verify', compact('certificate'));
    }
}