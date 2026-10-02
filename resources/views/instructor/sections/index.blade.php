@extends('layouts.app')

@section('title', 'Sections - ' . $course->title)
@section('page-title', 'Sections: ' . $course->title)
@section('page-subtitle', 'Kelola bab / section kursus')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => route('instructor.courses.edit', $course)],
        ['label' => 'Sections', 'url' => null],
    ]" />
@endsection

@section('content')
    <div x-data="sectionManager()" x-init="init()">
        @if (request('new'))
            <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-blue-800 dark:text-blue-300">Kursus berhasil dibuat! Langkah selanjutnya:</p>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                        Tambahkan <strong>Section</strong> (bab/topik) di bawah, lalu isi setiap section dengan <strong>Lesson</strong> (video, artikel, atau materi lain).
                        Contoh: Section "Pengenalan" berisi Lesson "Video Perkenalan" dan "Video Instalasi".
                    </p>
                </div>
            </div>
        @endif

        <div class="mb-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div class="flex gap-2">
                <a href="{{ route('instructor.courses.sections.create', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Section
                </a>
                <a href="{{ route('instructor.courses.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Kembali ke Kursus</a>
            </div>
            @if ($sections->count() > 1)
                <div class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8" />
                    </svg>
                    Geser (drag) baris untuk mengatur urutan
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <ul id="sortable-sections" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($sections as $section)
                    <li data-id="{{ $section->id }}" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move">
                        <div class="flex justify-between items-center gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-gray-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="9" cy="6" r="1.5" /><circle cx="9" cy="12" r="1.5" /><circle cx="9" cy="18" r="1.5" />
                                    <circle cx="15" cy="6" r="1.5" /><circle cx="15" cy="12" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                                </svg>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 dark:text-white truncate">{{ $section->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $section->total_lessons }} lesson •
                                        {{ $section->total_duration }} menit</div>
                                    @if (!$section->is_published)
                                        <span
                                            class="text-xs text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded-full mt-1 inline-block">Draft</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-end gap-1.5 shrink-0">
                                <a href="{{ route('instructor.courses.sections.lessons.index', [$course, $section]) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400"
                                    title="Kelola Lesson">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="hidden sm:inline">Lesson</span>
                                </a>
                                <a href="{{ route('instructor.courses.sections.edit', [$course, $section]) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400"
                                    title="Edit Section">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="hidden sm:inline">Edit</span>
                                </a>
                                <button @click="deleteSection({{ $section->id }})"
                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400"
                                    title="Hapus Section">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <p class="text-gray-600 dark:text-gray-300 font-medium">Belum ada section</p>
                        <p class="text-sm text-gray-400 mt-1">Section adalah bab/topik dalam kursus Anda. Klik "Tambah Section" untuk membuat yang pertama.</p>
                    </li>
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
