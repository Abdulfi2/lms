@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat belajar, ' . Auth::user()->name . '!')

@section('content')
    <div class="space-y-6">
        <x-notification-card />

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
                        <img src="{{ $enrollment->course->thumbnail ? Storage::url($enrollment->course->thumbnail) : 'https://placehold.co/400x200/769826/white?text=Course' }}"
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
                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/769826/white?text=Course' }}"
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
                            <a href="{{ route('student.courses.show', $course->slug) }}"
                                class="block mt-3 text-center text-sm text-primary hover:underline">Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- My Certificates -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Sertifikat Saya</h3>
                <a href="{{ route('student.certificates.index') }}" class="text-primary text-sm">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($latestCertificates as $cert)
                    <div class="flex items-center space-x-3 p-3 border rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">{{ $cert->course->title }}</p>
                            <p class="text-xs text-gray-500">Issued: {{ $cert->issued_at->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ $cert->url }}" target="_blank" class="text-primary text-sm">Download PDF</a>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-2">Belum ada sertifikat. Selesaikan kursus untuk mendapatkan
                        sertifikat.</p>
                @endforelse
            </div>
        </div>

        <!-- Gamification Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">{{ $gamification['current_level_icon'] ?? '🌱' }}</span>
                        <h3 class="text-xl font-bold">Level {{ $gamification['current_level'] ?? 1 }}:
                            {{ $gamification['current_level_name'] ?? 'Pemula' }}</h3>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">{{ number_format($gamification['total_points'] ?? 0) }} poin</p>
                    @if (!empty($gamification['next_level_name']))
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress ke Level {{ ($gamification['current_level'] ?? 1) + 1 }}</span>
                                <span>{{ $gamification['progress_percentage'] ?? 0 }}%</span>
                            </div>
                            <div class="w-64 bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full"
                                    style="width: {{ $gamification['progress_percentage'] ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Butuh
                                {{ number_format($gamification['points_to_next_level'] ?? 0) }} poin lagi</p>
                        </div>
                    @endif
                </div>

                <div class="text-center">
                    <div class="flex items-center space-x-1 text-yellow-500">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span class="text-2xl font-bold">{{ $gamification['streak_days'] ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Hari berturut-turut</p>
                </div>
            </div>

            <!-- Badges -->
            @if (!empty($gamification['badges']) && $gamification['badges']->count() > 0)
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <h4 class="text-sm font-semibold mb-2">Lencana yang Didapat</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($gamification['badges']->take(8) as $badge)
                            <div class="group relative">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    style="background: {{ $badge->badge->color }}20">
                                    <span class="text-xl">{{ $badge->badge->icon }}</span>
                                </div>
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                    {{ $badge->badge->name }}
                                </div>
                            </div>
                        @endforeach
                        @if ($gamification['badges']->count() > 8)
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold">
                                +{{ $gamification['badges']->count() - 8 }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
