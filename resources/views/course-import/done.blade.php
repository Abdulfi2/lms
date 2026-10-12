@extends('layouts.app')

@section('title', 'Impor Selesai')
@section('page-title', 'Impor Selesai')
@section('page-subtitle', 'Course berhasil dibuat sebagai draft. Lengkapi hal-hal di bawah sebelum diterbitkan.')

@php
    $course = $report['course'];
    $counts = $report['counts'];
@endphp

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $course->title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Draft untuk {{ $instructor->name }}: {{ $counts['sections'] }} section, {{ $counts['lessons'] }} lesson,
            {{ $counts['quizzes'] }} kuis ({{ $counts['questions'] }} soal), {{ $counts['assignments'] }} tugas.
        </p>
        <div class="flex flex-wrap gap-3 mt-4">
            <a href="{{ route($prefix . '.courses.edit', $course) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm font-medium">Buka course</a>
            <a href="{{ route($prefix . '.courses.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium">Daftar kursus</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Yang masih perlu Anda kerjakan</h3>
        <ul class="list-disc pl-5 space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
            <li>Unggah <strong>thumbnail</strong> course.</li>
            <li>Isi <strong>video_url</strong> pada lesson bertipe video yang masih kosong.</li>
            <li>Periksa dan terbitkan <strong>kuis</strong> dan <strong>tugas</strong> (keduanya dibuat belum terbit).</li>
            <li>Setelah semuanya benar, ubah status course dari draft.</li>
        </ul>
    </div>

    @if ($report['pending_uploads'])
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Lampiran materi yang perlu diunggah ({{ count($report['pending_uploads']) }})</h3>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                @foreach ($report['pending_uploads'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($report['skipped_categories'] || $warnings)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-yellow-400">
            <h3 class="font-semibold text-yellow-700 dark:text-yellow-400 mb-2">Peringatan</h3>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                @foreach ($report['skipped_categories'] as $name)
                    <li>Kategori "{{ $name }}" tidak dipasang ke course. Pilih manual di halaman edit course.</li>
                @endforeach
                @foreach ($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
