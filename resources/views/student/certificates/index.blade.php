{{-- resources/views/student/certificates/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Sertifikat Saya')
@section('page-title', 'Sertifikat')
@section('page-subtitle', 'Penghargaan atas pencapaian Anda')

@section('content')
    <div class="space-y-6">
        @if ($certificates->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Sertifikat</h3>
                <p class="text-gray-500 mt-1">Selesaikan kursus untuk mendapatkan sertifikat.</p>
                <a href="{{ route('courses.index') }}"
                    class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Jelajahi
                    Kursus</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($certificates as $cert)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 p-4 text-white text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L15 8.5L22 9.5L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9.5L9 8.5L12 2Z" />
                            </svg>
                            <h3 class="font-bold text-lg">{{ $cert->course->title }}</h3>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between text-sm text-gray-500 mb-2">
                                <span>Diterbitkan:</span>
                                <span>{{ $cert->issued_at->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500 mb-4">
                                <span>Nomor Sertifikat:</span>
                                <span class="font-mono text-xs">{{ $cert->certificate_number }}</span>
                            </div>
                            <div class="flex justify-between space-x-3">
                                <a href="{{ route('student.certificates.show', $cert) }}"
                                    class="flex-1 text-center px-3 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm">
                                    Lihat
                                </a>
                                <a href="{{ route('student.certificates.download', $cert) }}"
                                    class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
@endsection
