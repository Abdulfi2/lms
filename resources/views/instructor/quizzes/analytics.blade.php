@extends('layouts.app')

@section('title', 'Analitik Quiz - ' . $quiz->title)
@section('page-title', 'Analitik Quiz')
@section('page-subtitle', $quiz->title . ' — ' . $course->title)

@section('content')
<div class="space-y-6">
    <a href="{{ route('instructor.courses.quizzes.index', $course) }}" class="text-primary hover:underline text-sm">&larr; Kembali ke daftar quiz</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
        <p class="text-sm text-gray-500">Total Attempt Selesai</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalAttempts }}</p>
    </div>

    @if ($totalAttempts === 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center text-gray-500">
            Belum ada siswa yang mengerjakan quiz ini.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($questions as $data)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['question']->question }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $data['answered'] }} siswa menjawab</p>
                        </div>
                        @if ($data['correct_rate'] !== null)
                            <span class="px-3 py-1 rounded-full text-sm font-semibold shrink-0
                                {{ $data['correct_rate'] < 50 ? 'bg-red-100 text-red-800' : ($data['correct_rate'] < 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $data['correct_rate'] }}% benar
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-500 shrink-0">Belum dijawab</span>
                        @endif
                    </div>

                    @if ($data['options']->isNotEmpty())
                        <div class="mt-4 space-y-2">
                            @foreach ($data['options'] as $option)
                                @php
                                    $pct = $data['answered'] > 0 ? round(($option['selected_count'] / $data['answered']) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="{{ $option['is_correct'] ? 'text-green-700 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $option['text'] }} {{ $option['is_correct'] ? '(Jawaban Benar)' : '' }}
                                        </span>
                                        <span class="text-gray-400">{{ $option['selected_count'] }} ({{ $pct }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $option['is_correct'] ? 'bg-green-500' : 'bg-gray-400' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
