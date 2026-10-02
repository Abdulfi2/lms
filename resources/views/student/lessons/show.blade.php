@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)
@section('page-title', $lesson->title)
@section('page-subtitle', $course->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('student.my-courses')],
        ['label' => $course->title, 'url' => route('student.courses.show', $course->slug)],
        ['label' => $lesson->section->title, 'dropdown' => $sectionLessonsForBreadcrumb],
        ['label' => $lesson->title, 'url' => null],
    ]" />
@endsection

@section('content')
    <div x-data="lessonPlayer()" x-init="init()" class="max-w-5xl mx-auto">

        <!-- Progress Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">{{ $lesson->title }}</h1>
                        <p class="text-white/80 text-sm mt-1">{{ $course->title }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ round($enrollment->progress) }}%</div>
                        <div class="text-xs text-white/70">Progress Kursus</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="h-1.5 bg-gray-200 dark:bg-gray-700">
                <div class="h-full bg-green-500 transition-all duration-500" style="width: {{ $enrollment->progress }}%">
                </div>
            </div>

            <!-- Navigation Breadcrumb -->
            <div class="px-6 py-3 border-b dark:border-gray-700 flex justify-between items-center text-sm">
                <div class="flex items-center space-x-2 text-gray-500">
                    <a href="{{ route('student.courses.show', $course->slug) }}" class="hover:text-primary">Course</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-800 dark:text-gray-300">{{ $lesson->section->title }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-primary font-medium">{{ $lesson->title }}</span>
                </div>

                <!-- Points Badge -->
                <div class="flex items-center space-x-1 text-yellow-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-sm font-semibold">{{ $lesson->points }} poin</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Lesson Content -->
            <div class="lg:col-span-2 space-y-6">

                @if ($contentLocked)
                    <!-- Pre-Test Gate: materi disembunyikan sampai pre-test dikerjakan -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">Kerjakan Pre-Test Dulu</h2>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">{{ $pretest->title }}</p>
                        <p class="text-sm text-gray-500 mt-2 mb-6 max-w-md mx-auto">
                            Selesaikan pre-test ini untuk membuka materi lesson "{{ $lesson->title }}".
                            {{ $pretest->questions_count }} soal.
                        </p>
                        <a href="{{ route('student.quizzes.show', $pretest) }}"
                            class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition font-medium">
                            Mulai Pre-Test
                        </a>
                    </div>
                @else
                <!-- Lesson Type Badge -->
                <div class="flex items-center space-x-2">
                    @if ($lesson->type == 'video')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Video
                        </span>
                    @elseif($lesson->type == 'article')
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Artikel
                        </span>
                    @elseif($lesson->type == 'quiz')
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quiz
                        </span>
                    @endif
                    <span class="text-sm text-gray-500">Durasi: {{ $lesson->duration }} menit</span>
                </div>

                <!-- Video Player (if video) -->
                @if ($lesson->type === 'video' && $lesson->video_url)
                    <div class="bg-black rounded-xl overflow-hidden shadow-lg">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="{{ $lesson->video_url }}" frameborder="0"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                @endif

                <!-- Content Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">{{ $lesson->title }}</h2>

                        <div class="prose dark:prose-invert max-w-none">
                            {!! $lesson->content !!}
                        </div>
                    </div>
                </div>

                @if ($pretest && $pretestAttempt)
                    <div class="flex items-center gap-2 text-sm text-gray-500 -mt-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Pre-Test sudah dikerjakan ({{ round($pretestAttempt->percentage) }}%) —
                        <a href="{{ route('student.quizzes.show', $pretest) }}" class="text-primary hover:underline">lihat hasil</a>
                    </div>
                @endif

                <!-- Lesson Resources -->
                @if ($resources->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            Materi Pendukung
                        </h3>
                        <div class="space-y-3">
                            @foreach ($resources as $resource)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow-md transition">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-white dark:bg-gray-600 flex items-center justify-center shadow-sm">
                                            <i class="{{ $resource->icon }} text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-white">{{ $resource->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500">{{ $resource->formatted_file_size }} •
                                                {{ $resource->download_count }} unduhan</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.resources.download', [$course, $lesson->section, $lesson, $resource]) }}"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm flex items-center space-x-1 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($posttest)
                    <!-- Post-Test Card: menggantikan tombol "Tandai Selesai" manual -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    @if ($posttestPassed)
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-green-600 font-semibold">Lesson Selesai! (Post-Test Lulus)</span>
                                    @else
                                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-600">Kerjakan post-test untuk menyelesaikan lesson</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $posttest->title }} — {{ $posttest->questions_count }} soal, minimal {{ $posttest->passing_score }}% untuk lulus.
                                    @if ($posttestAttempt && !$posttestPassed)
                                        Percobaan terakhir: {{ round($posttestAttempt->percentage) }}%.
                                    @endif
                                    Dapatkan <span class="font-semibold text-yellow-500">{{ $lesson->points }} poin</span> setelah lulus.
                                </p>
                            </div>

                            @if ($posttestPassed)
                                <div class="px-6 py-2 bg-green-100 text-green-700 rounded-lg flex items-center space-x-2 shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Selesai</span>
                                </div>
                            @else
                                <a href="{{ route('student.quizzes.show', $posttest) }}"
                                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition font-medium shrink-0 whitespace-nowrap">
                                    {{ $posttestAttempt ? 'Coba Lagi Post-Test' : 'Kerjakan Post-Test' }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Complete Button (manual, lesson tanpa post-test) -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    @if ($isCompleted)
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-green-600 font-semibold">Lesson Selesai!</span>
                                    @else
                                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-600">Tandai sebagai selesai</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Dapatkan <span
                                        class="font-semibold text-yellow-500">{{ $lesson->points }} poin</span> setelah
                                    menyelesaikan lesson ini</p>
                            </div>

                            @if (!$isCompleted)
                                <button @click="completeLesson" :disabled="completing"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition flex items-center space-x-2">
                                    <span x-show="!completing">✓ Tandai Selesai</span>
                                    <span x-show="completing" class="flex items-center">
                                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Memproses...
                                    </span>
                                </button>
                            @else
                                <div class="px-6 py-2 bg-green-100 text-green-700 rounded-lg flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Selesai</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @endif
            </div>

            <!-- Sidebar Navigation -->
            <div class="space-y-6">
                <!-- Course Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Konten Kursus
                        </h3>
                    </div>
                    <div class="divide-y dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                        @foreach ($course->sections()->orderBy('order')->get() as $section)
                            <div x-data="{ open: {{ $section->id == $lesson->section->id ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="w-full flex justify-between items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-left">
                                    <div>
                                        <span class="font-medium text-sm">{{ $section->title }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ $section->lessons->count() }}
                                            lesson</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="bg-gray-50 dark:bg-gray-700/50">
                                    @foreach ($section->lessons()->orderBy('order')->get() as $l)
                                        @php
                                            $completed = \App\Models\LessonCompletion::where('user_id', auth()->id())
                                                ->where('lesson_id', $l->id)
                                                ->exists();
                                        @endphp
                                        <a href="{{ route('student.lessons.show', [$course, $l]) }}"
                                            class="flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $l->id == $lesson->id ? 'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-primary' : '' }}">
                                            <div class="flex items-center space-x-2">
                                                @if ($l->type == 'video')
                                                    <svg class="w-4 h-4 text-blue-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @elseif($l->type == 'quiz')
                                                    <svg class="w-4 h-4 text-purple-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-green-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                @endif
                                                <span
                                                    class="text-sm {{ $l->id == $lesson->id ? 'font-semibold text-primary' : 'text-gray-700 dark:text-gray-300' }}">{{ $l->title }}</span>
                                            </div>
                                            @if ($completed)
                                                <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between gap-3">
                    @if ($prevLesson)
                        <a href="{{ route('student.lessons.show', [$course, $prevLesson]) }}"
                            class="flex-1 text-center px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            ← Sebelumnya
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if ($nextLesson && !$isCompleted)
                        <a href="{{ route('student.lessons.show', [$course, $nextLesson]) }}"
                            class="flex-1 text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                            Selanjutnya →
                        </a>
                    @endif
                </div>

                <!-- User Stats -->
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Poin Anda</p>
                            <p class="text-2xl font-bold">{{ number_format($userPoint->total_points ?? 0) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm opacity-90">Level</p>
                            <p class="text-2xl font-bold">{{ $userPoint->current_level ?? 1 }}</p>
                        </div>
                    </div>
                    <div class="mt-2 w-full bg-white/30 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full"
                            style="width: {{ (($userPoint->total_points ?? 0) % 1000) / 10 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function lessonPlayer() {
                return {
                    completing: false,
                    init() {
                        // Mark as started when page loads (optional)
                    },
                    completeLesson() {
                        if (this.completing) return;
                        this.completing = true;

                        fetch('{{ route('student.lessons.complete', $lesson) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    time_spent: 0,
                                    watch_percentage: 100,
                                    last_position: 0
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);

                                    // Update progress bar
                                    const progressBar = document.querySelector('.bg-green-500');
                                    if (progressBar) {
                                        progressBar.style.width = data.progress + '%';
                                    }

                                    // Redirect if next lesson available
                                    if (data.next_lesson_url) {
                                        setTimeout(() => {
                                            window.location.href = data.next_lesson_url;
                                        }, 1500);
                                    } else {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                    }
                                } else {
                                    window.toast.error(data.message);
                                    this.completing = false;
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                window.toast.error('Terjadi kesalahan');
                                this.completing = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
