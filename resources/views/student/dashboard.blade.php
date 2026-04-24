@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat belajar, ' . Auth::user()->name . '!')

@section('content')
    <div x-data="studentDashboard()" x-init="init()" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Kursus Aktif</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCourses }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Selesai</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $completedCourses }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Progress Rata-rata</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ round($totalProgress) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Sertifikat</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $certificates }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Progress Belajar</h3>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-primary h-4 rounded-full" style="width: {{ round($totalProgress) }}%"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Anda telah menyelesaikan {{ $completedCourses }} dari {{ $totalCourses }}
                kursus</p>
        </div>

        <!-- My Courses -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Kursus Saya</h3>
                <a href="{{ route('student.my-courses') }}" class="text-primary hover:text-secondary text-sm">Lihat Semua
                    →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($enrollments->take(3) as $enrollment)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <img src="{{ $enrollment->course->thumbnail ? Storage::url($enrollment->course->thumbnail) : 'https://placehold.co/400x200/3B82F6/white?text=Course' }}"
                            class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">{{ $enrollment->course->title }}
                            </h4>
                            <div class="mb-3">
                                <div class="flex justify-between text-sm text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ round($enrollment->progress) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full" style="width: {{ $enrollment->progress }}%">
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('student.courses.show', $enrollment->course) }}"
                                class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                                Lanjutkan Belajar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recommended Courses -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Rekomendasi untuk Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($recommendedCourses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/3B82F6/white?text=Course' }}"
                            class="w-full h-32 object-cover">
                        <div class="p-3">
                            <h4 class="font-semibold text-gray-800 dark:text-white text-sm">
                                {{ Str::limit($course->title, 50) }}</h4>
                            <div class="flex items-center mt-2">
                                <div class="flex text-yellow-400 text-xs">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($course->average_rating))
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500 ml-1">({{ $course->rating_count }})</span>
                            </div>
                            <div class="mt-2">
                                <span class="font-bold text-primary">{{ $course->formatted_price }}</span>
                            </div>
                            <a href="{{ route('admin.courses.show', $course->slug) }}"
                                class="block mt-3 text-center text-sm text-primary hover:underline">Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
