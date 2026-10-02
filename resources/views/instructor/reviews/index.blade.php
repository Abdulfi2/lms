@extends('layouts.app')

@section('title', 'Ulasan Kursus')
@section('page-title', 'Manajemen Ulasan')
@section('page-subtitle', 'Lihat apa yang dikatakan siswa tentang kursus Anda dan berikan balasan.')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
        <form action="{{ route('instructor.reviews.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Kursus</label>
                <select name="course_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                    <option value="">Semua Kursus</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Rating</label>
                <select name="rating" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                    <option value="">Semua Rating</option>
                    @foreach([5,4,3,2,1] as $star)
                        <option value="{{ $star }}" {{ request('rating') == $star ? 'selected' : '' }}>
                            {{ $star }} Bintang
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition">
                    Filter
                </button>
                @if(request()->hasAny(['course_id', 'rating']))
                    <a href="{{ route('instructor.reviews.index') }}" class="ml-2 text-gray-500 hover:text-gray-700 p-2">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Review List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden p-6 border-l-4 {{ $review->rating >= 4 ? 'border-green-500' : ($review->rating == 3 ? 'border-yellow-500' : 'border-red-500') }}">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        <img src="{{ $review->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3" alt="">
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">{{ $review->user->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $review->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex text-yellow-400">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs font-medium text-gray-500 mt-1">{{ $review->course->title }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 dark:text-gray-300 italic">"{{ $review->comment }}"</p>
                </div>

                @if($review->instructor_response)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-4 border-l-2 border-primary">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-primary uppercase">Balasan Anda:</span>
                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($review->instructor_response['replied_at'])->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->instructor_response['content'] }}</p>
                    </div>
                @endif

                <div x-data="{ replying: false }">
                    <button @click="replying = !replying" class="text-sm font-medium text-primary hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        {{ $review->instructor_response ? 'Ubah Balasan' : 'Balas Ulasan' }}
                    </button>

                    <form x-show="replying" action="{{ route('instructor.reviews.update', $review->id) }}" method="POST" class="mt-4" style="display: none;">
                        @csrf
                        @method('PUT')
                        <textarea name="response" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-primary focus:border-primary" placeholder="Tulis balasan Anda di sini...">{{ $review->instructor_response['content'] ?? '' }}</textarea>
                        <div class="flex justify-end gap-2 mt-2">
                            <button type="button" @click="replying = false" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Batal</button>
                            <button type="submit" class="bg-primary text-white px-4 py-1 rounded-lg text-xs hover:bg-secondary transition">Kirim Balasan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="flex justify-center mb-4 text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada ulasan</h3>
                <p class="text-gray-500 mt-1">Ulasan dari siswa akan muncul di sini setelah mereka memberikan rating pada kursus Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
