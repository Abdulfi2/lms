@extends('layouts.client')

@section('title', 'Verifikasi Sertifikat')
@section('page-title', 'Verifikasi Sertifikat')
@section('page-subtitle', 'Cek keaslian sertifikat kursus')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden">
        @php
            $isExpired = $certificate->expires_at && $certificate->expires_at->isPast();
            $isValid = $certificate->is_verified && !$isExpired;
        @endphp

        <div class="p-6 text-center {{ $isValid ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            @if ($isValid)
                <div class="mx-auto w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-3">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h1 class="text-xl font-bold text-green-700 dark:text-green-400">Sertifikat Valid</h1>
            @else
                <div class="mx-auto w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-3">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h1 class="text-xl font-bold text-red-700 dark:text-red-400">
                    {{ $isExpired ? 'Sertifikat Sudah Kedaluwarsa' : 'Sertifikat Tidak Valid' }}
                </h1>
            @endif
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Nomor Sertifikat</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->certificate_number }}</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Nama Peserta</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->user->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Kursus</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white text-right">{{ $certificate->course->title ?? '-' }}</span>
            </div>
            <div class="flex justify-between border-b dark:border-gray-700 pb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Terbit</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ optional($certificate->issued_at)->format('d F Y') ?? '-' }}</span>
            </div>
            @if ($certificate->expires_at)
                <div class="flex justify-between pb-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Berlaku Hingga</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->expires_at->format('d F Y') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
