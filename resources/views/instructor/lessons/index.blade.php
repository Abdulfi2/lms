@extends('layouts.app')

@section('title', 'Lessons - ' . $section->title)
@section('page-title', 'Lessons: ' . $section->title)
@section('page-subtitle', 'Kursus: ' . $course->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => route('instructor.courses.edit', $course)],
        ['label' => 'Sections', 'url' => route('instructor.courses.sections.index', $course)],
        ['label' => $section->title, 'url' => route('instructor.courses.sections.edit', [$course, $section])],
        ['label' => 'Lessons', 'url' => null],
    ]" />
@endsection

@section('content')
    <div x-data="lessonManager()" x-init="init()">
        <div class="mb-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div class="flex gap-2">
                <a href="{{ route('instructor.courses.sections.lessons.create', [$course, $section]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Lesson
                </a>
                <a href="{{ route('instructor.courses.sections.index', $course) }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Kembali ke Section</a>
            </div>
            @if ($lessons->count() > 1)
                <div class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8" />
                    </svg>
                    Geser (drag) baris untuk mengatur urutan
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <ul id="sortable-lessons" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($lessons as $lesson)
                    <li data-id="{{ $lesson->id }}" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move">
                        <div class="flex justify-between items-center gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-gray-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="9" cy="6" r="1.5" /><circle cx="9" cy="12" r="1.5" /><circle cx="9" cy="18" r="1.5" />
                                    <circle cx="15" cy="6" r="1.5" /><circle cx="15" cy="12" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                                </svg>
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2">
                                        @if ($lesson->type == 'video')
                                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($lesson->type == 'article')
                                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        @elseif($lesson->type == 'quiz')
                                            <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                        <div class="font-semibold text-gray-800 dark:text-white truncate">{{ $lesson->title }}</div>
                                        <span class="text-xs text-gray-500 shrink-0">({{ $lesson->duration }} menit)</span>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1 truncate">{{ Str::limit($lesson->content, 80) }}</div>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-end gap-1.5 shrink-0">
                                <a href="{{ route('instructor.courses.sections.lessons.resources.index', [$course, $section, $lesson]) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
                                    title="Materi Tambahan (file pendukung)">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <span class="hidden sm:inline">Materi Tambahan</span>
                                </a>
                                <a href="{{ route('instructor.courses.sections.lessons.edit', [$course, $section, $lesson]) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400"
                                    title="Edit Lesson">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="hidden sm:inline">Edit</span>
                                </a>
                                <button @click="deleteLesson({{ $lesson->id }})"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400"
                                    title="Hapus Lesson">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-12 text-center">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="text-gray-600 dark:text-gray-300 font-medium">Belum ada lesson</p>
                        <p class="text-sm text-gray-400 mt-1">Lesson adalah materi belajar (video, artikel, dll) di dalam section ini. Klik "Tambah Lesson" untuk membuat yang pertama.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            function lessonManager() {
                return {
                    init() {
                        const el = document.getElementById('sortable-lessons');
                        if (el && el.children.length > 0) {
                            new Sortable(el, {
                                onEnd: () => {
                                    let ids = [...document.querySelectorAll('#sortable-lessons li')].map(li => li
                                        .dataset.id);
                                    fetch('{{ route('instructor.courses.sections.lessons.update-order', [$course, $section]) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            lessons: ids
                                        })
                                    }).then(res => res.json()).then(data => {
                                        if (data.success) window.toast.success('Urutan lesson disimpan');
                                        else window.toast.error('Gagal menyimpan urutan');
                                    });
                                }
                            });
                        }
                    },
                    deleteLesson(id) {
                        if (confirm('Yakin hapus lesson ini?')) {
                            fetch(`/instructor/courses/{{ $course->id }}/sections/{{ $section->id }}/lessons/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            }).then(res => res.json()).then(data => {
                                if (data.success) location.reload();
                                else window.toast.error(data.message);
                            });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
