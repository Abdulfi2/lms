@extends('layouts.app')

@section('title', 'Beri Review - ' . $course->title)
@section('page-title', 'Beri Review')
@section('page-subtitle', $course->title)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="text-5xl mb-2">📝</div>
                <h3 class="text-xl font-semibold">Bagikan Pengalaman Anda</h3>
                <p class="text-gray-500 text-sm mt-1">Rating dan review Anda membantu calon siswa lain memilih kursus
                    terbaik.</p>
            </div>

            <form action="{{ route('student.reviews.store', $course) }}" method="POST">
                @csrf

                <div class="mb-4 text-center">
                    <label class="block text-sm font-medium mb-2">Rating Anda</label>
                    <div class="flex justify-center space-x-2 text-3xl" x-data="{ rating: 0 }">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                class="focus:outline-none transition-transform hover:scale-110">
                                <span x-show="rating >= {{ $i }}" class="text-yellow-400">★</span>
                                <span x-show="rating < {{ $i }}" class="text-gray-300">★</span>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" x-model="rating">
                    </div>
                    @error('rating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Komentar (Opsional)</label>
                    <textarea name="comment" rows="5" class="w-full rounded-lg border-gray-300"
                        placeholder="Ceritakan pengalaman Anda mengikuti kursus ini...">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('student.courses.show', $course->slug) }}"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Kirim
                        Review</button>
                </div>
            </form>
        </div>
    </div>
@endsection
