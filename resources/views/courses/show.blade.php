@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', $course->short_description)

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white">
            <div class="flex flex-col md:flex-row justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">{{ $course->title }}</h1>
                    <p class="text-white/80 mt-2">{{ $course->short_description }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ ucfirst($course->level) }}</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->duration_total }} jam</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->total_lessons }} lessons</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->total_students }} siswa</span>
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center min-w-[200px]">
                    <div class="text-2xl font-bold">
                        @if ($course->price > 0)
                            @if ($course->sale_price && $course->sale_price < $course->price)
                                <span class="line-through text-sm text-white/70">Rp
                                    {{ number_format($course->price, 0, ',', '.') }}</span>
                                <div>Rp {{ number_format($course->sale_price, 0, ',', '.') }}</div>
                            @else
                                Rp {{ number_format($course->price, 0, ',', '.') }}
                            @endif
                        @else
                            Gratis
                        @endif
                    </div>
                    @if ($isEnrolled)
                        <a href="{{ route('student.courses.show', $course->slug) }}"
                            class="mt-3 block w-full text-center bg-white text-primary py-2 rounded-lg font-semibold hover:bg-gray-100">
                            Lanjutkan Belajar
                        </a>
                    @else
                        <form action="{{ route('student.courses.enroll', $course->slug) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="mt-3 w-full bg-white text-primary py-2 rounded-lg font-semibold hover:bg-gray-100">
                                Daftar Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bagian Kiri: Deskripsi & Materi -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-3">Tentang Kursus</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $course->description !!}
                    </div>
                </div>

                @if ($course->sections->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-3">Materi Kursus</h3>
                        <div class="space-y-3">
                            @foreach ($course->sections as $section)
                                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                    <button @click="open = !open"
                                        class="w-full flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="font-medium">{{ $section->title }}</span>
                                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-collapse class="mt-2 space-y-1">
                                        @foreach ($section->lessons as $lesson)
                                            <div
                                                class="flex items-center justify-between p-2 text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center space-x-2">
                                                    @if ($lesson->type == 'video')
                                                        <svg class="w-4 h-4 text-blue-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @elseif($lesson->type == 'article')
                                                        <svg class="w-4 h-4 text-green-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                        </svg>
                                                    @elseif($lesson->type == 'quiz')
                                                        <svg class="w-4 h-4 text-purple-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                    <span>{{ $lesson->title }}</span>
                                                </div>
                                                <span class="text-xs">{{ $lesson->duration }} min</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar: Info, Review, Lainnya -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <h3 class="font-semibold mb-2">Rating</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-bold">{{ number_format($averageRating, 1) }}</span>
                        <div class="flex text-yellow-400">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($averageRating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="text-gray-500">({{ $ratingCount }} ulasan)</span>
                    </div>
                </div>

                @if ($recentReviews->count())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                        <h3 class="font-semibold mb-3">Ulasan Terbaru</h3>
                        <div class="space-y-3">
                            @foreach ($recentReviews as $review)
                                <div class="text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">{{ $review->user->name }}</span>
                                        <div class="flex text-yellow-400 text-xs">
                                            @for ($i = 1; $i <= 5; $i++)
                                                {{ $i <= $review->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                                        {{ Str::limit($review->comment, 100) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($otherCourses->count())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                        <h3 class="font-semibold mb-3">Kursus Lain dari Instruktur Ini</h3>
                        <div class="space-y-2">
                            @foreach ($otherCourses as $other)
                                <a href="{{ route('courses.show', $other->slug) }}"
                                    class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                                    <p class="font-medium">{{ $other->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $other->formatted_price }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Box untuk guest: ajakan login -->
                @guest
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-center">
                        <p class="text-sm">Ingin mengakses kursus ini? <a href="{{ route('login') }}"
                                class="text-primary font-medium">Login</a> atau <a href="{{ route('register') }}"
                                class="text-primary font-medium">Daftar</a> untuk enroll.</p>
                    </div>
                @endguest
            </div>
        </div>
    </div>
@endsection
