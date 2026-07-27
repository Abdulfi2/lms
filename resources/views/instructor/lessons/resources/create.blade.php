@extends('layouts.app')

@section('title', 'Tambah Materi')
@section('page-title', 'Tambah Materi Tambahan')
@section('page-subtitle', 'Lesson: ' . $lesson->title)

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('instructor.courses.sections.lessons.resources.store', [$course, $section, $lesson]) }}"
        enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border-gray-300">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">File <span class="text-red-500">*</span></label>
            <input type="file" name="resource_file" required class="w-full">
            <p class="text-xs text-gray-400 mt-1">Maksimal 20MB.</p>
            @error('resource_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('instructor.courses.sections.lessons.resources.index', [$course, $section, $lesson]) }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
        </div>
    </form>
</div>
@endsection
