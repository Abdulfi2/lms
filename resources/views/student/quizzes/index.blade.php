@extends('layouts.app')

@section('title', 'Quiz Saya')
@section('page-title', 'Quiz')
@section('page-subtitle', 'Kerjakan quiz untuk menguji pemahaman Anda')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Quiz</p>
                <p class="text-2xl font-bold">{{ $totalQuizzes }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Quiz Selesai</p>
                <p class="text-2xl font-bold text-green-600">{{ $completedQuizzes }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Quiz Lulus</p>
                <p class="text-2xl font-bold text-blue-600">{{ $passedQuizzes }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
                <p class="text-2xl font-bold text-primary">{{ round($averageScore) }}%</p>
            </div>
        </div>

        <!-- Quiz List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Terbaik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attempt</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($quizzes as $quiz)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $quiz->questions_count ?? 0 }} soal •
                                            {{ $quiz->time_limit ? $quiz->time_limit . ' menit' : 'Tanpa batas' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $quiz->course->title }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($quiz->is_completed)
                                        @if ($quiz->is_passed)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓
                                                Lulus</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">✗ Tidak
                                                Lulus</span>
                                        @endif
                                    @elseif($quiz->attempt_count > 0)
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Belum
                                            Selesai</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Belum
                                            Dikerjakan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($quiz->best_score > 0)
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-semibold {{ $quiz->best_score >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                                                {{ round($quiz->best_score) }}%
                                            </span>
                                            <span class="text-xs text-gray-500">(Min: {{ $quiz->passing_score }}%)</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $quiz->attempt_count }} / {{ $quiz->attempts_allowed }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($quiz->attempt_count < $quiz->attempts_allowed)
                                        <a href="{{ route('student.quizzes.start', $quiz) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-secondary text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $quiz->attempt_count == 0 ? 'Mulai' : 'Kerjakan Ulang' }}
                                        </a>
                                    @else
                                        <button disabled
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-sm cursor-not-allowed">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                                            </svg>
                                            Habis
                                        </button>
                                    @endif
                                    @if ($quiz->is_completed)
                                        <a href="{{ route('student.quizzes.result', $quiz->user_attempt) }}"
                                            class="ml-2 inline-flex items-center px-3 py-1.5 border rounded-lg text-sm hover:bg-gray-50">
                                            Lihat Hasil
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>Belum ada quiz yang tersedia.</p>
                                    <p class="text-sm mt-1">Quiz akan muncul setelah instruktur membuat quiz di kursus yang
                                        Anda ikuti.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $quizzes->links() }}
            </div>
        </div>
    </div>
@endsection
