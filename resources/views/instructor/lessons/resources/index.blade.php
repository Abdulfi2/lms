@extends('layouts.app')

@section('title', 'Materi Tambahan - ' . $lesson->title)
@section('page-title', 'Materi Tambahan')
@section('page-subtitle', 'Lesson: ' . $lesson->title)

@section('content')
<div class="space-y-6">
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm text-blue-700 dark:text-blue-400">
        Materi Tambahan berbeda dari "Lampiran Utama" di halaman Edit Lesson — di sini Anda bisa mengunggah <strong>lebih dari satu file</strong> pendukung (contoh: modul PDF, file latihan, dll) untuk lesson ini.
    </div>

    <div class="flex justify-between items-center">
        <a href="{{ route('instructor.courses.sections.lessons.index', [$course, $section]) }}"
            class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">&larr; Kembali ke Lesson</a>
        <a href="{{ route('instructor.courses.sections.lessons.resources.create', [$course, $section, $lesson]) }}"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">+ Tambah Materi</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diunduh</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($resources as $resource)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $resource->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            <i class="{{ $resource->icon }} mr-1"></i>{{ $resource->file_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $resource->formatted_file_size }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $resource->download_count }}x</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('instructor.courses.sections.lessons.resources.edit', [$course, $section, $lesson, $resource]) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                            <form method="POST" action="{{ route('instructor.courses.sections.lessons.resources.destroy', [$course, $section, $lesson, $resource]) }}"
                                class="inline" onsubmit="return confirm('Hapus materi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada materi tambahan untuk lesson ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
