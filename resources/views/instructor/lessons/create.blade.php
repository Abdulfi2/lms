@extends('layouts.app')

@section('title', 'Tambah Lesson')
@section('page-title', 'Tambah Lesson Baru')
@section('page-subtitle', 'Section: ' . $section->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => route('instructor.courses.edit', $course)],
        ['label' => 'Sections', 'url' => route('instructor.courses.sections.index', $course)],
        ['label' => $section->title, 'url' => route('instructor.courses.sections.lessons.index', [$course, $section])],
        ['label' => 'Tambah Lesson', 'url' => null],
    ]" />
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div x-data="lessonForm()" x-init="init()" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('instructor.courses.sections.lessons.store', [$course, $section]) }}"
            enctype="multipart/form-data" id="lesson-form">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul Lesson <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Video Perkenalan"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe Lesson <span class="text-red-500">*</span></label>
                    <select name="type" x-model="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                        <option value="video">Video</option>
                        <option value="article">Artikel (Bacaan)</option>
                        <option value="quiz">Kuis</option>
                        <option value="assignment">Tugas</option>
                        <option value="live">Kelas Langsung (Live)</option>
                        <option value="discussion">Diskusi</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" x-show="type === 'quiz' || type === 'assignment'">
                        Catatan: untuk membuat soal kuis atau tugas sesungguhnya, gunakan menu "Quiz" atau "Tugas" pada kursus — lesson ini hanya sebagai penanda urutan materi.
                    </p>
                </div>

                <div x-show="type === 'video'" x-cloak>
                    <label class="block text-sm font-medium mb-1">Link Video</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                        placeholder="Tempel link video YouTube di sini"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p class="text-xs text-gray-500 mt-1">Tempel link video YouTube apapun (link biasa maupun link embed) — sistem akan menyesuaikan otomatis.</p>
                </div>

                <div x-show="!['quiz', 'discussion'].includes(type)" x-cloak>
                    <label class="block text-sm font-medium mb-1">Konten / Catatan Materi</label>
                    <div id="quill-content" class="bg-white dark:bg-gray-900 rounded-b-lg" style="min-height: 150px;"></div>
                    <textarea name="content" id="content-hidden" class="hidden">{{ old('content') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Tulis ringkasan materi, catatan, atau isi artikel di sini.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Durasi (menit)</label>
                        <input type="number" name="duration" value="{{ old('duration', 0) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Poin</label>
                        <input type="number" name="points" value="{{ old('points', 0) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <p class="text-xs text-gray-500 mt-1">Poin gamifikasi yang didapat siswa saat menyelesaikan lesson ini.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Lampiran Utama (opsional, satu file)</label>
                    <input type="file" name="attachment" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">
                        Format PDF, ZIP, atau MP4, maksimal 10MB. Butuh lebih dari satu file?
                        Gunakan menu <strong>Materi Tambahan</strong> setelah lesson ini disimpan.
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_free_preview" value="1" class="rounded">
                        <span class="ml-2">Bisa dilihat gratis (preview) tanpa daftar kursus</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_required" value="1" checked class="rounded">
                        <span class="ml-2">Wajib diselesaikan siswa</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft">Draft (belum terlihat siswa)</option>
                        <option value="published">Published (terlihat siswa)</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('instructor.courses.sections.lessons.index', [$course, $section]) }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                    Lesson</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
        <script>
            function lessonForm() {
                return {
                    type: '{{ old('type', 'video') }}',
                    quill: null,
                    init() {
                        // Quill tidak boleh diinisialisasi saat containernya masih disembunyikan
                        // (display:none via x-show) — hasilnya toolbar/editor bisa rusak. Tunda
                        // sampai field-nya benar-benar terlihat (tipe bukan quiz/discussion).
                        this.$nextTick(() => {
                            if (!['quiz', 'discussion'].includes(this.type)) {
                                this.initQuill();
                            }
                        });
                        this.$watch('type', (value) => {
                            if (!['quiz', 'discussion'].includes(value) && !this.quill) {
                                this.$nextTick(() => this.initQuill());
                            }
                        });
                    },
                    initQuill() {
                        if (this.quill || !document.getElementById('quill-content')) return;
                        this.quill = new Quill('#quill-content', {
                            theme: 'snow',
                            placeholder: 'Tulis ringkasan materi, catatan, atau isi artikel di sini',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });
                        const hidden = document.getElementById('content-hidden');
                        if (hidden.value) {
                            this.quill.root.innerHTML = hidden.value;
                        }
                        document.getElementById('lesson-form').addEventListener('submit', () => {
                            hidden.value = this.quill.root.innerHTML;
                        });
                    },
                }
            }
        </script>
    @endpush
@endsection
