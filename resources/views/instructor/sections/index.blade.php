@extends('layouts.app')

@section('title', 'Sections - ' . $course->title)
@section('page-title', 'Sections: ' . $course->title)
@section('page-subtitle', 'Kelola bab / section kursus')

@section('content')
    <div x-data="sectionManager()" x-init="init()">
        <div class="mb-4 flex justify-between items-center">
            <div>
                <a href="{{ route('instructor.courses.sections.create', $course) }}"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">+ Tambah Section</a>
                <a href="{{ route('instructor.courses.index') }}"
                    class="px-4 py-2 border rounded-lg ml-2 hover:bg-gray-100">Kembali ke Kursus</a>
            </div>
            <div class="text-sm text-gray-500">Drag & drop untuk mengurutkan section</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <ul id="sortable-sections" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($sections as $section)
                    <li data-id="{{ $section->id }}" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $section->title }}</div>
                                <div class="text-sm text-gray-500">{{ $section->total_lessons }} lessons •
                                    {{ $section->total_duration }} menit</div>
                                @if (!$section->is_published)
                                    <span
                                        class="text-xs text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded-full mt-1 inline-block">Draft</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('instructor.courses.sections.lessons.index', [$course, $section]) }}"
                                    class="text-green-600 hover:text-green-800 mr-3" title="Lessons">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </a>
                                <a href="{{ route('instructor.courses.sections.edit', [$course, $section]) }}"
                                    class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button @click="deleteSection({{ $section->id }})" class="text-red-600 hover:text-red-800"
                                    title="Hapus">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center text-gray-500">Belum ada section. Silakan tambah section pertama.</li>
                @endforelse
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            function sectionManager() {
                return {
                    init() {
                        const el = document.getElementById('sortable-sections');
                        if (el && el.children.length > 0) {
                            new Sortable(el, {
                                onEnd: () => {
                                    let ids = [...document.querySelectorAll('#sortable-sections li')].map(li => li
                                        .dataset.id);
                                    fetch('{{ route('instructor.courses.sections.update-order', $course) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            sections: ids
                                        })
                                    }).then(res => res.json()).then(data => {
                                        if (data.success) window.toast.success('Urutan section disimpan');
                                        else window.toast.error('Gagal menyimpan urutan');
                                    });
                                }
                            });
                        }
                    },
                    deleteSection(id) {
                        if (confirm('Yakin hapus section ini? Semua lesson di dalamnya akan terhapus.')) {
                            fetch(`/instructor/courses/{{ $course->id }}/sections/${id}`, {
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
