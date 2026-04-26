@extends('layouts.app')

@section('title', 'Sertifikat - ' . $certificate->course->title)
@section('page-title', 'Sertifikat Kelulusan')
@section('page-subtitle', $certificate->course->title)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- Preview Sertifikat -->
            <div class="p-8 text-center border-b dark:border-gray-700">
                <iframe src="{{ $certificate->url ?: Storage::url($certificate->file_path) }}"
                    class="w-full h-[500px] border-0" frameborder="0">
                </iframe>
            </div>

            <!-- Informasi Sertifikat -->
            <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><strong>Penerima:</strong> {{ $certificate->user->name }}</div>
                    <div><strong>Kursus:</strong> {{ $certificate->course->title }}</div>
                    <div><strong>Nomor Sertifikat:</strong> <code>{{ $certificate->certificate_number }}</code></div>
                    <div><strong>Tanggal Terbit:</strong> {{ $certificate->issued_at->format('d F Y') }}</div>
                    <div><strong>Instruktur:</strong> {{ $certificate->course->instructor->name }}</div>
                    <div><strong>Kode Verifikasi:</strong> <code>{{ $certificate->verification_code }}</code></div>
                </div>

                <div class="mt-6 flex justify-center space-x-4">
                    <a href="{{ route('student.certificates.download', $certificate) }}"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        📥 Download PDF
                    </a>
                    <a href="{{ route('student.certificates.print', $certificate) }}" target="_blank"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        🖨️ Cetak Sertifikat
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
