@extends('layouts.app')

@section('title', 'Tambah Section')
@section('page-title', 'Tambah Section Baru')
@section('page-subtitle', 'Untuk kursus: ' . $course->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => route('instructor.courses.edit', $course)],
        ['label' => 'Sections', 'url' => route('instructor.courses.sections.index', $course)],
        ['label' => 'Tambah Section', 'url' => null],
    ]" />
@endsection

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('instructor.courses.sections.store', $course) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Section <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                        (Opsional)</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description') }}</textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" value="1"
                        {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan (dapat dilihat siswa)</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('instructor.courses.sections.index', $course) }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                    Section</button>
            </div>
        </form>
    </div>
@endsection
