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

            @if ($hasPendingEssayGrading)
                <div class="px-6 py-3 bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-100 dark:border-yellow-900 text-sm text-yellow-800 dark:text-yellow-400">
                    ⏳ Ada soal essay yang masih menunggu penilaian instruktur. Nilai akhir Anda bisa berubah setelah dinilai.
                </div>
            @endif

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
                @if (!$attempt->is_passed && $attemptCount < $attempt->quiz->attempts_allowed)
                    <a href="{{ route('student.quizzes.start', $attempt->quiz) }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Coba Lagi
                    </a>
                @endif
            </div>
        </div>

        <!-- Pembahasan per soal -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pembahasan</h3>

            @foreach ($questions as $idx => $question)
                @php $answer = $answers->get($question->id); @endphp
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-gray-800 dark:text-white">
                            <span class="text-gray-400">{{ $idx + 1 }}.</span> {{ $question->question }}
                        </p>
                        @if ($question->type === 'essay')
                            @if ($answer && $answer->graded_at)
                                <span class="text-sm font-semibold shrink-0 text-primary">{{ $answer->points_earned }}/{{ $question->points }} poin</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 shrink-0">Menunggu penilaian</span>
                            @endif
                        @elseif ($answer && $answer->is_correct)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 shrink-0">Benar</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 shrink-0">Salah</span>
                        @endif
                    </div>

                    @if ($question->type === 'essay')
                        <div class="mt-3">
                            <p class="text-xs text-gray-400 mb-1">Jawaban Anda</p>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ $answer->answer_text ?? '(Tidak dijawab)' }}
                            </div>
                            @if ($answer && $answer->graded_at && $answer->feedback)
                                <p class="text-xs text-gray-400 mt-2 mb-1">Catatan Instruktur</p>
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm text-blue-800 dark:text-blue-300">{{ $answer->feedback }}</div>
                            @endif
                        </div>
                    @elseif ($question->type === 'multiple_choice')
                        <div class="mt-3 space-y-1.5">
                            @foreach ($question->options as $option)
                                @php
                                    $isSelected = $answer && $answer->selected_option_id === $option->id;
                                @endphp
                                <div class="text-sm px-3 py-1.5 rounded-lg flex items-center justify-between
                                    {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400' : ($isSelected ? 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400' : 'text-gray-600 dark:text-gray-400') }}">
                                    <span>{{ $option->option_text }}</span>
                                    <span class="text-xs shrink-0">
                                        @if ($option->is_correct) Jawaban Benar @endif
                                        @if ($isSelected && !$option->is_correct) (Pilihan Anda) @endif
                                        @if ($isSelected && $option->is_correct) (Pilihan Anda) @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($question->type === 'true_false')
                        <div class="mt-3 space-y-1.5">
                            @foreach ($question->options as $option)
                                @php
                                    $isSelected = $answer && $answer->selected_option_id === $option->id;
                                @endphp
                                <div class="text-sm px-3 py-1.5 rounded-lg flex items-center justify-between
                                    {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400' : ($isSelected ? 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400' : 'text-gray-600 dark:text-gray-400') }}">
                                    <span>{{ $option->option_text }}</span>
                                    <span class="text-xs shrink-0">
                                        @if ($option->is_correct) Jawaban Benar @endif
                                        @if ($isSelected) (Pilihan Anda) @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($question->explanation)
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 mb-1">Pembahasan</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $question->explanation }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
