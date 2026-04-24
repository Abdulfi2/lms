@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', $course->short_description)

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Course Header -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">{{ $course->title }}</h1>
                    <p class="text-white/80 mt-2">{{ $course->short_description }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ ucfirst($course->level) }}</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->duration_total }} jam</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->total_lessons }} lessons</span>
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center min-w-[150px]">
                    <div class="text-2xl font-bold">{{ round($enrollment->progress) }}%</div>
                    <div class="text-sm">Progress</div>
                    <div class="w-full bg-white/30 rounded-full h-2 mt-2">
                        <div class="bg-white h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content: Sections & Lessons -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">Materi Kursus</h3>
                    </div>
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($sections as $section)
                            <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="w-full flex justify-between items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700 text-left">
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $section->title }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ $section->total_lessons }}
                                            lessons</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="bg-gray-50 dark:bg-gray-700/50">
                                    @foreach ($section->lessons as $lesson)
                                        <a href="{{ route('student.lessons.show', [$course, $lesson]) }}"
                                            class="flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $lesson->id == optional($currentLesson)->id ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                                            <div class="flex items-center space-x-3">
                                                @if ($lesson->type === 'video')
                                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @elseif($lesson->type === 'article')
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                        </path>
                                                    </svg>
                                                @elseif($lesson->type === 'quiz')
                                                    <svg class="w-5 h-5 text-purple-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                @endif
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300">{{ $lesson->title }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">{{ $lesson->duration }} min</span>
                                                @if ($lesson->is_free_preview && !$enrollment)
                                                    <span class="text-xs text-green-600">Preview</span>
                                                @endif
                                                @php
                                                    $completed = \App\Models\LessonCompletion::where(
                                                        'user_id',
                                                        auth()->id(),
                                                    )
                                                        ->where('lesson_id', $lesson->id)
                                                        ->exists();
                                                @endphp
                                                @if ($completed)
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Tentang Kursus</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Instruktur:</strong> {{ $course->instructor->name }}</p>
                        <p><strong>Level:</strong> {{ ucfirst($course->level) }}</p>
                        <p><strong>Durasi:</strong> {{ $course->duration_total }} jam</p>
                        <p><strong>Total Lesson:</strong> {{ $course->total_lessons }}</p>
                        <p><strong>Siswa:</strong> {{ $course->total_students }}</p>
                        <p><strong>Rating:</strong> {{ number_format($course->average_rating, 1) }} / 5
                            ({{ $course->rating_count }} rating)</p>
                    </div>
                    @if ($course->has_certificate)
                        <div class="mt-3 pt-3 border-t dark:border-gray-700">
                            <div class="flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                <span class="text-sm">Sertifikat setelah menyelesaikan kursus</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
