@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', 'Menunggu konfirmasi pembayaran')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 text-center bg-yellow-50 dark:bg-yellow-900/20">
            <div class="mx-auto w-16 h-16 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-3">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-xl font-bold text-yellow-800 dark:text-yellow-400">Menunggu Konfirmasi Pembayaran</h1>
            <p class="text-sm text-yellow-700 dark:text-yellow-500 mt-2">
                Pendaftaran Anda ke kursus <strong>{{ $course->title }}</strong> sudah tercatat, tapi materi kursus
                baru bisa diakses setelah pembayaran dikonfirmasi oleh admin.
            </p>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $course->formatted_price }}</span>
            </div>
            <div class="flex justify-between pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Daftar</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $enrollment->enrolled_at?->format('d F Y') ?? '-' }}</span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-300">
                Sudah melakukan pembayaran? Konfirmasi ke admin melalui
                <strong class="text-primary">support@lms.com</strong> beserta bukti transfer agar akses kursus
                segera diaktifkan.
            </div>

            <div class="flex justify-center pt-2">
                <a href="{{ route('student.my-courses') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600 text-sm">
                    Kembali ke Kursus Saya
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
