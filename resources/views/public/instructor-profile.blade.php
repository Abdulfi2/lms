@extends('layouts.client')

@section('title', $instructor->name)
@section('page-title', $instructor->name)

@section('content')
@php
    $professional = $instructor->profile->professional_info ?? [];
    $social = $instructor->profile->social_media ?? [];
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            <img src="{{ $instructor->avatar_url }}" class="w-24 h-24 rounded-full object-cover shrink-0">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $instructor->name }}</h1>
                @if (!empty($professional['headline']))
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $professional['headline'] }}</p>
                @endif

                <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                    <span>{{ $courses->count() }} Kursus</span>
                    <span>{{ number_format($totalStudents) }} Siswa</span>
                    @if ($avgRating > 0)
                        <span>&#9733; {{ $avgRating }} Rating</span>
                    @endif
                </div>

                @if (!empty(array_filter($social)))
                    <div class="flex gap-3 mt-4">
                        @foreach (['website', 'linkedin', 'twitter', 'instagram', 'youtube'] as $platform)
                            @if (!empty($social[$platform]))
                                <a href="{{ $social[$platform] }}" target="_blank" rel="noopener"
                                    class="text-sm text-primary hover:underline capitalize">{{ $platform }}</a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if (!empty($professional['bio']))
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Tentang</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $professional['bio'] }}</p>
            </div>
        @endif

        @if (!empty($professional['expertise']))
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Keahlian</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($professional['expertise'] as $skill)
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-700 dark:text-gray-300">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Kursus dari {{ $instructor->name }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($courses as $course)
                <a href="{{ route('courses.show', $course->slug) }}"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/3B82F6/white?text=Course' }}"
                        class="w-full h-32 object-cover">
                    <div class="p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $course->formatted_price }}</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-sm">Belum ada kursus yang dipublikasikan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
