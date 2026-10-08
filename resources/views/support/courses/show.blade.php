@extends('layouts.app')

@section('title', 'Detail Kursus')
@section('page-title', 'Detail Kursus')
@section('page-subtitle', 'Informasi lihat-saja untuk membantu troubleshooting')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('support.courses.index') }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke daftar</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $course->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">oleh {{ $course->instructor->name ?? '-' }}</p>
            </div>
            <span class="px-2 py-1 text-xs rounded-full {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : ($course->status === 'pending' ? 'bg-blue-100 text-blue-800' : ($course->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                {{ ucfirst($course->status) }}
            </span>
        </div>

        @if ($course->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mt-3">
                @foreach ($course->categories as $category)
                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Total Enrollment</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $course->enrollments_count }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $course->created_at->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Enrollment Terbaru</h3>
        </div>
        <div class="divide-y dark:divide-gray-700">
            @forelse ($recentEnrollments as $enrollment)
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $enrollment->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->enrolled_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $enrollment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($enrollment->payment_status) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Belum ada enrollment.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
