@extends('layouts.client')

@section('title', 'FAQ')
@section('page-title', 'Pertanyaan yang Sering Diajukan')
@section('page-subtitle', 'Temukan jawaban atas pertanyaan umum seputar ' . config('app.name', 'LMS'))

@section('content')
<div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">
    @php
        $faqs = [
            ['q' => 'Bagaimana cara mendaftar kursus?', 'a' => 'Buat akun sebagai siswa, telusuri katalog kursus, lalu klik "Daftar Sekarang" pada halaman detail kursus. Untuk kursus berbayar, selesaikan pembayaran sesuai instruksi yang muncul.'],
            ['q' => 'Apakah ada kursus gratis?', 'a' => 'Ya, sebagian kursus tersedia secara gratis. Kursus gratis dapat langsung diakses setelah Anda mendaftar tanpa proses pembayaran.'],
            ['q' => 'Bagaimana cara mendapatkan sertifikat?', 'a' => 'Selesaikan seluruh materi dan penilaian pada kursus yang memiliki fitur sertifikat. Sertifikat akan diterbitkan otomatis dan dapat diunduh atau diverifikasi melalui halaman sertifikat Anda.'],
            ['q' => 'Bagaimana jika saya lupa password?', 'a' => 'Klik "Lupa Password" pada halaman login, lalu ikuti instruksi yang dikirimkan ke email terdaftar Anda.'],
            ['q' => 'Bagaimana cara menjadi instruktur?', 'a' => 'Daftar dengan memilih peran instruktur saat registrasi. Akun instruktur baru akan melalui proses persetujuan oleh admin sebelum dapat mempublikasikan kursus.'],
            ['q' => 'Bagaimana cara menghubungi dukungan?', 'a' => 'Gunakan halaman Kontak untuk mengirim pesan kepada tim kami. Kami akan merespons secepat mungkin melalui email yang Anda cantumkan.'],
        ];
    @endphp

    @foreach ($faqs as $i => $faq)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                class="w-full flex justify-between items-center px-5 py-4 text-left">
                <span class="font-medium text-gray-800 dark:text-white">{{ $faq['q'] }}</span>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open === {{ $i }} }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open === {{ $i }}" x-collapse class="px-5 pb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ $faq['a'] }}
            </div>
        </div>
    @endforeach
</div>
@endsection
