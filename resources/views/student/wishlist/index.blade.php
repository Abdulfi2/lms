@extends('layouts.app')

@section('title', 'Wishlist Saya')
@section('page-title', 'Wishlist Saya')
@section('page-subtitle', 'Kursus yang ingin Anda ambil nanti')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($wishlists as $wishlist)
            @php $course = $wishlist->course; @endphp
            @if ($course)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/769826/white?text=Course' }}"
                        class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 dark:text-white mb-1">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-500 mb-3">{{ $course->instructor->name ?? '-' }}</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-primary">{{ $course->formatted_price }}</span>
                            <div class="flex space-x-2">
                                <a href="{{ route('courses.show', $course->slug) }}" class="px-3 py-1.5 bg-primary text-white rounded-lg text-sm hover:bg-secondary">
                                    Lihat
                                </a>
                                <form action="{{ route('student.wishlist.destroy', $wishlist) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 border rounded-lg text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 dark:border-gray-600">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Wishlist masih kosong</h3>
                <p class="mt-1 text-gray-500">Simpan kursus yang menarik untuk diambil nanti.</p>
                <a href="{{ route('courses.index') }}" class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                    Jelajahi Kursus
                </a>
            </div>
        @endforelse
    </div>

    {{ $wishlists->links() }}
</div>
@endsection
