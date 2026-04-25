@extends('layouts.app')

@section('title', 'Daftar Kursus')
@section('page-title', 'Kursus Kami')
@section('page-subtitle', 'Temukan kursus terbaik untuk tingkatkan skill Anda')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filter -->
        <aside class="lg:w-64 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Kategori</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('courses.index') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary flex justify-between">
                            <span>Semua</span>
                            <span class="text-xs text-gray-400">({{ $categories->sum('courses_count') }})</span>
                        </a>
                    </li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                                class="text-gray-600 dark:text-gray-400 hover:text-primary flex justify-between {{ request('category') == $cat->slug ? 'text-primary font-medium' : '' }}">
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
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('level'), ['level' => 'beginner'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('level') == 'beginner' ? 'text-primary font-medium' : '' }}">
                            Beginner
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('level'), ['level' => 'intermediate'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('level') == 'intermediate' ? 'text-primary font-medium' : '' }}">
                            Intermediate
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('level'), ['level' => 'advanced'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('level') == 'advanced' ? 'text-primary font-medium' : '' }}">
                            Advanced
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('level'), ['level' => 'all_levels'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('level') == 'all_levels' ? 'text-primary font-medium' : '' }}">
                            All Levels
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Harga</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('price'), ['price' => 'free'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('price') == 'free' ? 'text-primary font-medium' : '' }}">
                            Gratis
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index', array_merge(request()->except('price'), ['price' => 'paid'])) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary {{ request('price') == 'paid' ? 'text-primary font-medium' : '' }}">
                            Berbayar
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content: Course Grid -->
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
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full text-xs">
                                    {{ ucfirst($course->level) }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                    {{ number_format($course->average_rating, 1) }}
                                    <span class="text-gray-400 ml-1">({{ $course->rating_count }})</span>
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-800 dark:text-white mt-2">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ Str::limit($course->short_description, 70) }}</p>
                            <div class="mt-3 flex justify-between items-center">
                                <div>
                                    @if ($course->price > 0)
                                        @if ($course->sale_price && $course->sale_price < $course->price)
                                            <span class="text-xs text-gray-400 line-through">Rp
                                                {{ number_format($course->price, 0, ',', '.') }}</span>
                                            <span class="font-bold text-primary">Rp
                                                {{ number_format($course->sale_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="font-bold text-primary">Rp
                                                {{ number_format($course->price, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="font-bold text-green-600">Gratis</span>
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}"
                                    class="text-primary hover:underline text-sm">Detail →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 mt-2">Tidak ada kursus yang ditemukan.</p>
                        <a href="{{ route('courses.index') }}" class="text-primary mt-2 inline-block">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
@endsection
