@extends('layouts.app')

@section('title', 'Edit Review - ' . $course->title)
@section('page-title', 'Edit Review')
@section('page-subtitle', $course->title)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="text-5xl mb-3">✏️</div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Review</h3>
                <p class="text-gray-500 text-sm mt-1">Perbarui rating dan komentar Anda</p>
            </div>

            <form action="{{ route('student.reviews.update', $course) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Rating -->
                <div class="mb-6 text-center">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating Anda</label>
                    <div x-data="{ rating: {{ $review->rating }} }" class="flex justify-center space-x-2 text-4xl">
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
                        placeholder="Ceritakan pengalaman Anda mengikuti kursus ini...">{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('student.courses.show', $course->slug) }}"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        Update Review
                    </button>
                </div>
            </form>

            <!-- Delete Button -->
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <form action="{{ route('student.reviews.destroy', $course) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus review ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                        Hapus Review
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
