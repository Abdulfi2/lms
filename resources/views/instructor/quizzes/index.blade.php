{{-- resources/views/instructor/quizzes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quiz - ' . $course->title)
@section('page-title', 'Quiz')
@section('page-subtitle', 'Kelola quiz untuk kursus: ' . $course->title)

@section('content')
    <div x-data="quizManager()" x-init="init()" class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('instructor.courses.index') }}"
                    class="text-primary hover:underline inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke kursus
                </a>
            </div>
            <a href="{{ route('instructor.courses.quizzes.create', $course) }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Quiz Baru
            </a>
        </div>

        @if ($quizzes->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada quiz</h3>
                <p class="text-gray-500 mt-1">Buat quiz pertama untuk menguji pemahaman siswa.</p>
                <a href="{{ route('instructor.courses.quizzes.create', $course) }}"
                    class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                    + Buat Quiz
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($quizzes as $quiz)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white text-lg">{{ $quiz->title }}</h3>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                            {{ $quiz->questions_count }} soal
                                        </span>
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                            {{ $quiz->attempts_allowed }}x attempts
                                        </span>
                                        @if ($quiz->time_limit)
                                            <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                {{ $quiz->time_limit }} menit
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <x-status-badge :status="$quiz->is_published" />
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 line-clamp-2">
                                {{ Str::limit($quiz->description, 100) }}
                            </p>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs text-gray-500">
                                    Attempts: {{ $quiz->total_attempts ?? 0 }}
                                </div>
                                <div class="flex space-x-2">
                                    @if ($quiz->pending_essay_count > 0)
                                        <a href="{{ route('instructor.courses.quizzes.grading', [$course, $quiz]) }}"
                                            class="relative text-orange-600 hover:text-orange-800" title="Nilai Soal Essay">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="absolute -top-2 -right-2 bg-orange-600 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{ $quiz->pending_essay_count }}</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('instructor.courses.quizzes.analytics', [$course, $quiz]) }}"
                                        class="text-purple-600 hover:text-purple-800" title="Analitik">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('instructor.courses.quizzes.edit', [$course, $quiz]) }}"
                                        class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button
                                        @click="togglePublish({{ $quiz->id }}, {{ $quiz->is_published ? 'true' : 'false' }})"
                                        class="{{ $quiz->is_published ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}"
                                        title="{{ $quiz->is_published ? 'Unpublish' : 'Publish' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if ($quiz->is_published)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            @endif
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete({{ $quiz->id }}, @json($quiz->title))"
                                        class="text-red-600 hover:text-red-800" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Hapus Quiz</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus quiz "<span x-text="deleteQuizTitle"></span>"? Semua data soal
                        dan jawaban akan hilang.
                    </p>
                    <div class="mt-5 flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                        <button @click="deleteQuiz"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function quizManager() {
                return {
                    deleteModalOpen: false,
                    deleteQuizId: null,
                    deleteQuizTitle: '',

                    togglePublish(quizId, currentStatus) {
                        const newStatus = !currentStatus;
                        fetch(`/instructor/courses/{{ $course->id }}/quizzes/${quizId}/toggle-publish`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    is_published: newStatus
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    window.toast.error(data.message || 'Gagal mengubah status');
                                }
                            })
                            .catch(() => window.toast.error('Terjadi kesalahan'));
                    },

                    confirmDelete(id, title) {
                        this.deleteQuizId = id;
                        this.deleteQuizTitle = title;
                        this.deleteModalOpen = true;
                    },

                    deleteQuiz() {
                        fetch(`/instructor/courses/{{ $course->id }}/quizzes/${this.deleteQuizId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    window.toast.error(data.message);
                                    this.deleteModalOpen = false;
                                }
                            })
                            .catch(() => window.toast.error('Terjadi kesalahan'));
                    }
                }
            }
        </script>
    @endpush
@endsection
