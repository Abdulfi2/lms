@extends('layouts.app')

@section('title', $lesson->title)
@section('page-title', $lesson->title)
@section('page-subtitle', $course->title)

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Video Player (if video) -->
            @if ($lesson->type === 'video' && $lesson->video_url)
                <div class="aspect-video bg-black">
                    <iframe class="w-full h-full" src="{{ $lesson->video_url }}" frameborder="0" allowfullscreen></iframe>
                </div>
            @endif

            <!-- Content -->
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">{{ $lesson->title }}</h1>
                <div class="prose dark:prose-invert max-w-none">
                    {!! $lesson->content !!}
                </div>

                <!-- Complete Button -->
                @if (!$isCompleted)
                    <div class="mt-6 flex justify-end">
                        <button @click="completeLesson"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Tandai Selesai
                        </button>
                    </div>
                @else
                    <div class="mt-6 flex justify-end">
                        <span class="px-6 py-2 bg-green-100 text-green-800 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Selesai
                        </span>
                    </div>
                @endif

                <!-- Navigation between lessons -->
                <div class="mt-6 pt-6 border-t dark:border-gray-700 flex justify-between">
                    @if ($prevLesson)
                        <a href="{{ route('student.lessons.show', [$course, $prevLesson]) }}"
                            class="text-primary hover:underline">← Sebelumnya</a>
                    @else
                        <div></div>
                    @endif

                    @if ($nextLesson)
                        <a href="{{ route('student.lessons.show', [$course, $nextLesson]) }}"
                            class="text-primary hover:underline">Selanjutnya →</a>
                    @else
                        <a href="{{ route('student.courses.show', $course) }}" class="text-primary hover:underline">Selesai,
                            kembali ke kursus →</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function completeLesson() {
                fetch('{{ route('student.lessons.complete', $lesson) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        window.toast.success('Lesson selesai!');
                        location.reload();
                    } else {
                        window.toast.error('Gagal menyimpan progress');
                    }
                });
            }
        </script>
    @endpush
@endsection
