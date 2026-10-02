@extends('layouts.app')

@section('title', 'Tambah Lesson')
@section('page-title', 'Tambah Pelajaran Baru')
@section('page-subtitle', 'Section: ' . $section->title)

@section('content')
    <div x-data="lessonForm()" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form @submit.prevent="submitForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Lesson <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Lesson <span
                            class="text-red-500">*</span></label>
                    <select x-model="form.type"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="video">Video</option>
                        <option value="article">Artikel</option>
                        <option value="quiz">Quiz</option>
                        <option value="assignment">Tugas</option>
                        <option value="live">Live Class</option>
                        <option value="discussion">Diskusi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konten (untuk
                        artikel)</label>
                    <textarea x-model="form.content" rows="6"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi
                            (menit)</label>
                        <input type="number" x-model="form.duration" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poin</label>
                        <input type="number" x-model="form.points" class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL Video (jika tipe
                        video)</label>
                    <input type="url" x-model="form.video_url" class="w-full rounded-lg border-gray-300"
                        placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.is_free_preview" class="rounded border-gray-300">
                        <span class="ml-2 text-sm">Gratis preview (bisa ditonton tanpa enroll)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.is_published" class="rounded border-gray-300">
                        <span class="ml-2 text-sm">Publikasikan</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.lessons.index', [$course, $section]) }}"
                    class="px-4 py-2 border rounded-lg">Batal</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg disabled:opacity-50">
                    <span x-show="!loading">Simpan Lesson</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function lessonForm() {
                return {
                    form: {
                        title: '',
                        type: 'video',
                        content: '',
                        duration: 0,
                        points: 0,
                        video_url: '',
                        is_free_preview: false,
                        is_published: true,
                    },
                    errors: {},
                    loading: false,
                    submitForm() {
                        this.loading = true;
                        this.errors = {};
                        fetch('{{ route('admin.lessons.store', [$course, $section]) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(this.form)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    setTimeout(() => {
                                        window.location.href =
                                            '{{ route('admin.lessons.index', [$course, $section]) }}';
                                    }, 800);
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            })
                            .catch(() => {
                                window.toast.error('Terjadi kesalahan');
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
