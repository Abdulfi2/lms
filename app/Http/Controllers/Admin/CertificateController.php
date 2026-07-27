<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Daftar semua sertifikat yang sudah terbit.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'revoked') {
                $query->where('is_verified', false);
            } elseif ($request->status === 'verified') {
                $query->where('is_verified', true);
            }
        }

        $certificates = $query->latest('issued_at')->paginate(15)->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Detail satu sertifikat.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course']);

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Cabut/pulihkan status verifikasi sertifikat. Sertifikat yang dicabut akan
     * gagal saat divalidasi lewat halaman verifikasi publik (QR code), tapi
     * data & filenya tetap disimpan (bukan dihapus permanen).
     */
    public function toggleVerified(Certificate $certificate)
    {
        $certificate->update(['is_verified' => !$certificate->is_verified]);

        return back()->with(
            'success',
            $certificate->is_verified
                ? 'Sertifikat berhasil dipulihkan (verified).'
                : 'Sertifikat berhasil dicabut (revoked).'
        );
    }

    /**
     * Hapus sertifikat secara permanen beserta file PDF-nya.
     */
    public function destroy(Certificate $certificate)
    {
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat berhasil dihapus permanen.');
    }
}
