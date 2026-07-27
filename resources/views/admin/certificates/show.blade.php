@extends('layouts.app')

@section('title', 'Detail Sertifikat')
@section('page-title', 'Detail Sertifikat')
@section('page-subtitle', $certificate->certificate_number)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('admin.certificates.index') }}" class="text-primary hover:underline inline-flex items-center text-sm">
        &larr; Kembali ke Daftar Sertifikat
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 text-center {{ $certificate->is_verified ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <h1 class="text-xl font-bold {{ $certificate->is_verified ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                {{ $certificate->is_verified ? 'Sertifikat Verified' : 'Sertifikat Revoked' }}
            </h1>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Nomor Sertifikat</span>
                <span class="text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $certificate->certificate_number }}</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Siswa</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->user->name ?? '-' }} ({{ $certificate->user->email ?? '-' }})</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Kursus</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white text-right">{{ $certificate->course->title ?? '-' }}</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Terbit</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ optional($certificate->issued_at)->format('d F Y H:i') ?? '-' }}</span>
            </div>
            @if ($certificate->expires_at)
                <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Berlaku Hingga</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->expires_at->format('d F Y') }}</span>
                </div>
            @endif
            <div class="flex justify-between pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Kode Verifikasi</span>
                <span class="text-xs font-mono text-gray-500">{{ $certificate->verification_code }}</span>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('certificate.verify', $certificate->verification_code) }}" target="_blank"
                    class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">
                    Lihat Halaman Verifikasi Publik
                </a>
                @if ($certificate->file_path)
                    <a href="{{ Storage::url($certificate->file_path) }}" target="_blank"
                        class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">
                        Lihat PDF
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-3 pt-4 border-t dark:border-gray-700">
                <form action="{{ route('admin.certificates.toggle-verified', $certificate) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm text-white {{ $certificate->is_verified ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                        {{ $certificate->is_verified ? 'Cabut Sertifikat (Revoke)' : 'Pulihkan Sertifikat (Restore)' }}
                    </button>
                </form>
                <form action="{{ route('admin.certificates.destroy', $certificate) }}" method="POST"
                    onsubmit="return confirm('Hapus sertifikat ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-sm">
                        Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
