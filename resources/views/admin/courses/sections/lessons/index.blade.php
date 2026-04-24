@extends('layouts.app')

@section('title', 'Lessons - ' . $section->title)
@section('page-title', 'Lessons: ' . $section->title)
@section('page-subtitle', 'Section dari kursus ' . $course->title)

@section('content')
    <div x-data="lessonManager()" x-init="init()">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('admin.sections.index', $course) }}" class="text-primary hover:underline">&larr;
                    Kembali ke sections</a>
            </div>
            <a href="{{ route('admin.lessons.create', [$course, $section]) }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                + Tambah Lesson
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Pelajaran (Drag & drop untuk urutan)</h3>
            </div>
            <ul id="sortable-lessons" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($lessons as $lesson)
                    <li data-id="{{ $lesson->id }}"
                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move transition">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $lesson->title }}</span>
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $lesson->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $lesson->status }}
                                    </span>
                                    <span
                                        class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800">{{ ucfirst($lesson->type) }}</span>
                                    @if ($lesson->is_free_preview)
                                        <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800">Preview</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-4 mt-2 ml-8 text-xs text-gray-500">
                                    <span>Durasi: {{ $lesson->duration }} menit</span>
                                    <span>Poin: {{ $lesson->points }}</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.lessons.edit', [$course, $section, $lesson]) }}"
                                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                    Edit
                                </a>
                                <button @click="deleteLesson({{ $lesson->id }})"
                                    class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center text-gray-500">Belum ada pelajaran. Silakan tambah lesson pertama.</li>
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
                                animation: 150,
                                onEnd: () => {
                                    let ids = [...document.querySelectorAll('#sortable-lessons li')].map(li => li
                                        .dataset.id);
                                    fetch('{{ route('admin.courses.sections.lessons.update-order', [$course, $section]) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                lessons: ids
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.success) window.toast.success(data.message);
                                            else window.toast.error(data.message);
                                        })
                                        .catch(() => window.toast.error('Gagal menyimpan urutan'));
                                }
                            });
                        }
                    },
                    deleteLesson(id) {
                        if (confirm('Yakin ingin menghapus pelajaran ini?')) {
                            fetch(`/admin/courses/{{ $course->id }}/sections/{{ $section->id }}/lessons/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        window.toast.success(data.message);
                                        location.reload();
                                    } else {
                                        window.toast.error(data.message);
                                    }
                                })
                                .catch(() => window.toast.error('Gagal menghapus lesson'));
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
