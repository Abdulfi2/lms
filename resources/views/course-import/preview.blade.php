@extends('layouts.app')

@section('title', 'Pratinjau Impor')
@section('page-title', 'Pratinjau Impor Kursus')
@section('page-subtitle', 'Periksa ringkasan di bawah sebelum membuat course.')

@php
    $summary = $result['summary'];
    $stats = [
        'Section' => $summary['sections'],
        'Lesson' => $summary['lessons'],
        'Kuis' => $summary['quizzes'],
        'Soal' => $summary['questions'],
        'Tugas' => $summary['assignments'],
        'Lampiran (manual)' => $summary['resources'],
    ];
@endphp

@section('content')
<div class="max-w-4xl space-y-6">
    @if ($errors->any())
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <p class="text-xs text-gray-500 dark:text-gray-400">Course</p>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ $summary['title'] }}</h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($stats as $label => $value)
                <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-3 text-center">
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $value }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @if ($result['errors'])
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <h3 class="font-semibold text-red-600 mb-1">{{ count($result['errors']) }} kesalahan, paket belum bisa diimpor</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Nomor dalam kurung siku dimulai dari 0 (mis. sections[0] adalah section pertama). Perbaiki JSON lalu unggah ulang.</p>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                @foreach ($result['errors'] as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($result['warnings'])
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-yellow-400">
            <h3 class="font-semibold text-yellow-700 dark:text-yellow-400 mb-2">Peringatan (tidak menghalangi impor)</h3>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                @foreach ($result['warnings'] as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($result['notes'])
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-2">Catatan dari AI agent</h3>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                @foreach ($result['notes'] as $note)
                    <li class="{{ str_contains($note, 'PERLU VERIFIKASI') ? 'text-orange-600 dark:text-orange-400' : '' }}">{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-wrap items-end gap-3">
        @if ($token)
            <form method="POST" action="{{ route($prefix . '.courses.import.store') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                @if ($isAdmin)
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Instruktur pemilik course</label>
                        <select name="instructor_id" required
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-primary focus:border-primary">
                            <option value="">Pilih instruktur...</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected(old('instructor_id') == $instructor->id)>{{ $instructor->name }} ({{ $instructor->email }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                    Buat sebagai draft
                </button>
            </form>
        @endif

        <a href="{{ route($prefix . '.courses.import.create') }}"
            class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">
            {{ $token ? 'Batal' : 'Unggah ulang' }}
        </a>
    </div>
</div>
@endsection
