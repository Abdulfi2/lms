@extends('layouts.app')

@section('title', 'Kursus Saya')
@section('page-title', 'Kursus Saya')
@section('page-subtitle', 'Semua kursus yang Anda ikuti')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($enrollments as $enrollment)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <div class="relative">
                        <img src="{{ $enrollment->course->thumbnail ? Storage::url($enrollment->course->thumbnail) : 'https://placehold.co/400x200/769826/white?text=Course' }}"
                            class="w-full h-40 object-cover">
                        <div class="absolute top-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded-full">
                            {{ round($enrollment->progress) }}%
                        </div>
                        @if ($enrollment->payment_status !== 'paid')
                            <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                Menunggu Pembayaran
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 dark:text-white mb-1">{{ $enrollment->course->title }}</h4>
                        <p class="text-sm text-gray-500 mb-3">{{ Str::limit($enrollment->course->short_description, 80) }}
                        </p>
                        <div class="mb-3">
                            <div class="flex justify-between text-sm text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ round($enrollment->progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Terdaftar:
                                {{ $enrollment->created_at->format('d/m/Y') }}</span>
                            <a href="{{ route('student.courses.show', $enrollment->course) }}"
                                class="px-4 py-2 {{ $enrollment->payment_status !== 'paid' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-primary hover:bg-secondary' }} text-white rounded-lg text-sm">
                                {{ $enrollment->payment_status !== 'paid' ? 'Cek Status' : 'Lanjutkan' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center col-span-full">
                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Belum ada kursus</h3>
                    <p class="mt-1 text-gray-500">Anda belum mendaftar kursus apapun. Jelajahi kursus dan mulai belajar!</p>
                    <a href="{{ route('courses.index') }}"
                        class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Jelajahi
                        Kursus</a>
                </div>
            @endforelse
        </div>

        {{ $enrollments->links() }}
    </div>
@endsection
