@extends('layouts.app')

@section('title', $course->title)
@section('page-title', 'Detail Kursus')
@section('page-subtitle', $course->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.courses.index') }}" class="text-primary hover:underline inline-flex items-center text-sm">
            &larr; Kembali ke Daftar Kursus
        </a>
        <a href="{{ route('admin.courses.edit', $course) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            Edit Kursus
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if ($course->thumbnail)
            <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-56 object-cover">
        @endif
        <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
                <x-status-badge :status="$course->status" />
                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ ucfirst($course->level) }}</span>
                @if ($course->is_featured)
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Unggulan</span>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $course->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">
                Instruktur: {{ $course->instructor->name ?? '-' }}
                @if ($course->instructor)
                    ({{ $course->instructor->email }})
                @endif
            </p>

            @if ($course->short_description)
                <p class="mt-4 text-gray-600 dark:text-gray-300">{{ $course->short_description }}</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-sm">
                <div><strong>Harga</strong><br>{{ $course->formatted_price }}</div>
                <div><strong>Bahasa</strong><br>{{ strtoupper($course->language) }}</div>
                <div><strong>Durasi</strong><br>{{ $course->duration_total ?? 0 }} jam</div>
                <div><strong>Siswa Terdaftar</strong><br>{{ number_format($course->total_students) }}</div>
                <div><strong>Rating</strong><br>{{ number_format($course->average_rating, 1) }} ({{ $course->rating_count }} ulasan)</div>
                <div><strong>Total Section</strong><br>{{ $course->sections->count() }}</div>
                <div><strong>Total Lesson</strong><br>{{ $course->sections->sum(fn($s) => $s->lessons->count()) }}</div>
                <div><strong>Sertifikat</strong><br>{{ $course->has_certificate ? 'Ya' : 'Tidak' }}</div>
            </div>

            @if ($course->categories->isNotEmpty())
                <div class="mt-4">
                    <strong class="text-sm">Kategori:</strong>
                    @foreach ($course->categories as $category)
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full ml-1">{{ $category->name }}</span>
                    @endforeach
                </div>
            @endif

            @if ($course->tags->isNotEmpty())
                <div class="mt-2">
                    <strong class="text-sm">Tag:</strong>
                    @foreach ($course->tags as $tag)
                        <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded-full ml-1">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Kurikulum -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white">Kurikulum</h3>
            <a href="{{ route('admin.sections.index', $course) }}" class="text-sm text-primary hover:underline">Kelola Section &amp; Lesson</a>
        </div>
        <div class="divide-y dark:divide-gray-700">
            @forelse ($course->sections->sortBy('order') as $section)
                <div class="p-4">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $section->title }}</p>
                    <ul class="mt-2 space-y-1">
                        @forelse ($section->lessons->sortBy('order') as $lesson)
                            <li class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-gray-300 mr-2"></span>
                                {{ $lesson->title }}
                                <span class="ml-2 text-xs text-gray-400">({{ ucfirst($lesson->type) }})</span>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">Belum ada lesson.</li>
                        @endforelse
                    </ul>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">Belum ada section.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
