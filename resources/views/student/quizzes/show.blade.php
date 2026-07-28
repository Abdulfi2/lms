@extends('layouts.app')

@section('title', $quiz->title)
@section('page-title', $quiz->title)
@section('page-subtitle', $quiz->course->title)

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $quiz->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $quiz->course->title }}</p>
                </div>
                @if ($isCompleted)
                    @if ($isPassed)
                        <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">✓ Lulus</span>
                    @else
                        <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">✗ Tidak Lulus</span>
                    @endif
                @endif
            </div>

            @if ($quiz->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">{{ $quiz->description }}</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Jumlah Soal</p>
                    <p class="font-semibold text-gray-800 dark:text-white mt-1">{{ $quiz->questions_count ?? $quiz->questions()->count() }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Waktu</p>
                    <p class="font-semibold text-gray-800 dark:text-white mt-1">{{ $quiz->time_limit ? $quiz->time_limit . ' menit' : 'Tanpa batas' }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Nilai Terbaik</p>
                    <p class="font-semibold {{ $bestScore >= $quiz->passing_score ? 'text-green-600' : 'text-gray-800 dark:text-white' }} mt-1">
                        {{ $bestScore > 0 ? round($bestScore) . '%' : '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Attempt</p>
                    <p class="font-semibold text-gray-800 dark:text-white mt-1">{{ $attemptCount }} / {{ $quiz->attempts_allowed }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <a href="{{ route('student.quizzes.index') }}" class="text-sm text-primary hover:underline">&larr; Kembali ke daftar quiz</a>

                @if ($remainingAttempts > 0)
                    <a href="{{ route('student.quizzes.start', $quiz) }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        {{ $attemptCount > 0 ? 'Coba Lagi' : 'Mulai Quiz' }}
                    </a>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 rounded-lg text-sm">Batas percobaan tercapai</span>
                @endif
            </div>
        </div>

        @if ($lastAttempt && $lastAttempt->status === 'completed')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Hasil Terakhir</h3>
                <a href="{{ route('student.quizzes.result', $lastAttempt) }}" class="text-primary hover:underline text-sm">
                    Lihat detail hasil percobaan terakhir &rarr;
                </a>
            </div>
        @endif
    </div>
@endsection
