@extends('layouts.app')

@section('title', 'Beri Review - ' . $course->title)
@section('page-title', 'Beri Review')
@section('page-subtitle', $course->title)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="text-5xl mb-3">📝</div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Bagikan Pengalaman Anda</h3>
                <p class="text-gray-500 text-sm mt-1">Rating dan review Anda membantu calon siswa lain memilih kursus
                    terbaik.</p>
                <div class="mt-2 inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                    Progress kursus: {{ round($enrollment->progress) }}%
                </div>
            </div>

            <form action="{{ route('student.reviews.store', $course) }}" method="POST">
                @csrf

                <!-- Rating -->
                <div class="mb-6 text-center">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating Anda</label>
                    <div x-data="{ rating: 0 }" class="flex justify-center space-x-2 text-4xl">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                class="focus:outline-none transition-transform hover:scale-110">
                                <span x-show="rating >= {{ $i }}" class="text-yellow-400">★</span>
                                <span x-show="rating < {{ $i }}" class="text-gray-300">★</span>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" x-model="rating" required>
                    </div>
                    @error('rating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Komentar</label>
                    <textarea name="comment" rows="5"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary"
                        placeholder="Ceritakan pengalaman Anda mengikuti kursus ini..."></textarea>
                    @error('comment')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info -->
                <div
                    class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm text-yellow-800 dark:text-yellow-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Review Anda akan ditampilkan setelah disetujui oleh admin.</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('student.courses.show', $course->slug) }}"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        Kirim Review
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
