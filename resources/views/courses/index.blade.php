@extends('layouts.app')

@section('title', 'Daftar Kursus')
@section('page-title', 'Kursus')
@section('page-subtitle', 'Temukan kursus terbaik untuk meningkatkan skill Anda')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filter -->
        <aside class="lg:w-64 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Kategori</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('courses.index') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary">Semua</a></li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                                class="text-gray-600 dark:text-gray-400 hover:text-primary flex justify-between">
                                {{ $cat->name }}
                                <span class="text-xs text-gray-400">({{ $cat->courses_count }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Level</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('courses.index', ['level' => 'beginner']) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary">Beginner</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'intermediate']) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary">Intermediate</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'advanced']) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary">Advanced</a></li>
                </ul>
            </div>
        </aside>

        <!-- Course Grid -->
        <div class="flex-1">
            <div class="mb-4">
                <form method="GET" action="{{ route('courses.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kursus..."
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:bg-gray-700">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Cari</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/3B82F6/white?text=Course' }}"
                            class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 dark:text-white">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($course->short_description, 60) }}</p>
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
                            <div class="mt-2 flex justify-between items-center">
                                <span class="font-bold text-primary">{{ $course->formatted_price }}</span>
                                <a href="{{ route('courses.show', $course->slug) }}"
                                    class="text-sm text-primary hover:underline">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Tidak ada kursus yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $courses->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
