@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . $attempt->quiz->title)
@section('page-title', 'Hasil Quiz')
@section('page-subtitle', $attempt->quiz->title)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Result Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 text-center border-b">
                @if ($attempt->is_passed)
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-green-600">Selamat! Anda Lulus</h2>
                @else
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-red-600">Belum Lulus</h2>
                @endif
                <p class="text-gray-500 mt-2">Silakan pelajari materi dan coba lagi</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-gray-500 text-sm">Nilai Anda</p>
                        <p class="text-3xl font-bold text-primary">{{ round($attempt->percentage) }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Nilai Minimal</p>
                        <p class="text-3xl font-bold">{{ $attempt->quiz->passing_score }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Benar</p>
                        <p class="text-3xl font-bold text-green-600">{{ $correctCount ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Soal</p>
                        <p class="text-3xl font-bold">{{ $totalQuestions ?? $questions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-between">
                <a href="{{ route('student.quizzes.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    ← Kembali ke Quiz
                </a>
                @if (!$attempt->is_passed && $attempt->attempt_count < $attempt->quiz->attempts_allowed)
                    <a href="{{ route('student.quizzes.start', $attempt->quiz) }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Coba Lagi
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
