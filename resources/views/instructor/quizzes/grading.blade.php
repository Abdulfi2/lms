@extends('layouts.app')

@section('title', 'Penilaian Soal Essay - ' . $quiz->title)
@section('page-title', 'Penilaian Soal Essay')
@section('page-subtitle', $quiz->title . ' — ' . $course->title)

@section('content')
<div class="space-y-6">
    <a href="{{ route('instructor.courses.quizzes.index', $course) }}" class="text-primary hover:underline text-sm">&larr; Kembali ke daftar quiz</a>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500">Menunggu Penilaian</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $attempts->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500">Sudah Dinilai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $gradedCount }}</p>
        </div>
    </div>

    @if ($attempts->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
            <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 dark:text-gray-300 font-medium">Tidak ada jawaban essay yang menunggu penilaian</p>
            <p class="text-sm text-gray-400 mt-1">Semua siswa yang mengerjakan soal essay pada quiz ini sudah dinilai.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($attempts as $attempt)
                    <li class="p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-800 dark:text-white truncate">{{ $attempt->user->name }}</div>
                            <div class="text-sm text-gray-500">Dikumpulkan: {{ $attempt->completed_at->format('d M Y, H:i') }}</div>
                        </div>
                        <a href="{{ route('instructor.courses.quizzes.attempts.grade', [$course, $quiz, $attempt]) }}"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm font-medium shrink-0">
                            Nilai Jawaban
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
