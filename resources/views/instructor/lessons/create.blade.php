@extends('layouts.app')

@section('title', 'Tambah Lesson')
@section('page-title', 'Tambah Lesson Baru')
@section('page-subtitle', 'Section: ' . $section->title)

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('instructor.courses.sections.lessons.store', [$course, $section]) }}"
            enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul Lesson <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe Lesson <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full rounded-lg border-gray-300" required>
                        <option value="video">Video</option>
                        <option value="article">Artikel</option>
                        <option value="quiz">Kuis</option>
                        <option value="assignment">Tugas</option>
                        <option value="live">Live Class</option>
                        <option value="discussion">Diskusi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Konten</label>
                    <textarea name="content" rows="5" class="w-full rounded-lg border-gray-300">{{ old('content') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Durasi (menit)</label>
                        <input type="number" name="duration" value="{{ old('duration', 0) }}"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Poin</label>
                        <input type="number" name="points" value="{{ old('points', 0) }}"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL Video (jika tipe video)</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                        placeholder="https://www.youtube.com/embed/..." class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Lampiran (PDF, ZIP, MP4)</label>
                    <input type="file" name="attachment" class="w-full">
                </div>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_free_preview" value="1" class="rounded">
                        <span class="ml-2">Preview gratis</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_required" value="1" checked class="rounded">
                        <span class="ml-2">Wajib diselesaikan</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('instructor.courses.sections.lessons.index', [$course, $section]) }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                    Lesson</button>
            </div>
        </form>
    </div>
@endsection
