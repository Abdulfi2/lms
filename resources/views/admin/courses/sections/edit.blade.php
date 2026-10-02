@extends('layouts.app')

@section('title', 'Edit Section')
@section('page-title', 'Edit Section')
@section('page-subtitle', 'Kursus: ' . $course->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus', 'url' => route('admin.courses.index')],
        ['label' => $course->title, 'url' => route('admin.courses.edit', $course)],
        ['label' => 'Sections', 'url' => route('admin.sections.index', $course)],
        ['label' => $section->title, 'url' => null],
    ]" />
@endsection

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.sections.update', [$course, $section]) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Section <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $section->title) }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('description', $section->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" value="1"
                        {{ old('is_published', $section->is_published) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-primary focus:ring-primary">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.sections.index', $course) }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">Batal</a>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Update Section</button>
            </div>
        </form>
    </div>
@endsection
