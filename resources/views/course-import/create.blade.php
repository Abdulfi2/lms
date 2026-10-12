@extends('layouts.app')

@section('title', 'Impor Kursus')
@section('page-title', 'Impor Kursus dari JSON')
@section('page-subtitle', 'Buat course lengkap (section, lesson, kuis, tugas) sekaligus dari paket JSON buatan AI agent.')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{ mode: '{{ old('json') ? 'paste' : 'file' }}' }">
    @if ($errors->any())
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-1">Unggah paket course</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Paket diperiksa dulu dan ditampilkan sebagai pratinjau. Belum ada yang tersimpan sampai Anda menekan
            tombol konfirmasi.
        </p>

        <form method="POST" action="{{ route($prefix . '.courses.import.preview') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="flex gap-2 text-sm">
                <button type="button" @click="mode = 'file'"
                    :class="mode === 'file' ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-3 py-1.5 rounded-lg">Unggah berkas</button>
                <button type="button" @click="mode = 'paste'"
                    :class="mode === 'paste' ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-3 py-1.5 rounded-lg">Tempel JSON</button>
            </div>

            <div x-show="mode === 'file'">
                <input type="file" name="file" accept=".json,application/json,text/plain"
                    class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-primary file:text-white hover:file:bg-secondary">
                <p class="text-xs text-gray-400 mt-1">Berkas .json, maksimal 2 MB.</p>
            </div>

            <div x-show="mode === 'paste'" style="display: none;">
                <textarea name="json" rows="12" placeholder='{ "course": { ... }, "sections": [ ... ] }'
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 font-mono text-xs focus:ring-primary focus:border-primary">{{ old('json') }}</textarea>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                Periksa paket
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 text-sm text-gray-600 dark:text-gray-300 space-y-2">
        <h3 class="font-semibold text-gray-800 dark:text-white">Yang terjadi saat impor</h3>
        <ul class="list-disc pl-5 space-y-1">
            <li>Course selalu dibuat sebagai <strong>draft</strong>; kuis dan tugas dibuat <strong>belum terbit</strong>.</li>
            <li>Semuanya dibuat dalam satu langkah. Kalau ada yang gagal, tidak ada data setengah jadi.</li>
            <li>Kategori dan tag dicocokkan berdasarkan nama. Tag yang belum ada dibuat otomatis.
                @unless ($isAdmin)
                    Kategori baru hanya bisa dibuat admin; kategori yang belum ada akan dilewati.
                @endunless
            </li>
            <li><strong>Tidak ikut terimpor:</strong> thumbnail, lampiran materi, dan video. Unggah/isi manual setelah impor
                (daftarnya ditampilkan di akhir).</li>
        </ul>
    </div>
</div>
@endsection
