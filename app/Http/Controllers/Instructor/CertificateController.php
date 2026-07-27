<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    /**
     * Daftar sertifikat yang telah diterbitkan untuk siswa di kursus instruktur ini.
     */
    public function index(Request $request)
    {
        $certificates = $this->baseQuery($request)
            ->latest('issued_at')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.certificates.index', compact('certificates'));
    }

    /**
     * Export daftar sertifikat (sesuai filter aktif) sebagai CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $certificates = $this->baseQuery($request)->latest('issued_at')->get();

        $filename = 'sertifikat-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($certificates) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No. Sertifikat', 'Nama Siswa', 'Email', 'Kursus', 'Tanggal Terbit', 'Status']);

            foreach ($certificates as $certificate) {
                fputcsv($handle, [
                    $certificate->certificate_number,
                    $certificate->user->name ?? '-',
                    $certificate->user->email ?? '-',
                    $certificate->course->title ?? '-',
                    optional($certificate->issued_at)->format('Y-m-d'),
                    $certificate->is_verified ? 'Terverifikasi' : 'Dicabut',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function baseQuery(Request $request)
    {
        $query = Certificate::with(['user', 'course'])
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        return $query;
    }
}
