@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', $course->short_description)

@php
    $userReview = App\Models\Review::where('user_id', Auth::id())->where('course_id', $course->id)->first();
    $canReview = $enrollment && $enrollment->progress >= 50;
    $reviews = App\Models\Review::approved()->with('user')->where('course_id', $course->id)->latest()->paginate(5);
    $isCourseCompleted = $enrollment->progress >= 100;
    $certificate = App\Models\Certificate::where('user_id', Auth::id())->where('course_id', $course->id)->first();
@endphp

@section('content')
    <div x-data="coursePlayer()" x-init="init()" class="max-w-6xl mx-auto space-y-6">
        <!-- Certificate Banner (Muncul jika course selesai) -->
        @if ($isCourseCompleted)
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L15 8.5L22 9.5L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9.5L9 8.5L12 2Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">🎉 Selamat! Anda Telah Menyelesaikan Kursus</h3>
                            <p class="text-white/80 text-sm">Anda telah menyelesaikan seluruh materi dengan baik.</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        @if ($certificate)
                            <a href="{{ route('student.certificates.show', $certificate) }}"
                                class="px-4 py-2 bg-white text-green-600 rounded-lg hover:bg-gray-100 transition font-semibold">
                                Lihat Sertifikat
                            </a>
                            <a href="{{ route('student.certificates.download', $certificate) }}"
                                class="px-4 py-2 bg-white/20 border border-white text-white rounded-lg hover:bg-white/30 transition flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Download PDF</span>
                            </a>
                            <form action="{{ route('student.certificates.regenerate', $course->slug) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-white text-green-600 rounded-lg hover:bg-gray-100 transition font-semibold">
                                    Generate Ulang
                                </button>
                            </form>
                        @else
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="px-4 py-2 bg-yellow-500 text-white rounded-lg flex items-center space-x-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Sertifikat sedang diproses...</span>
                                </div>
                                <form action="{{ route('student.certificates.regenerate', $course->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-white text-green-600 rounded-lg hover:bg-gray-100 transition font-semibold">
                                        Generate Ulang
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Header Kursus -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">{{ $course->title }}</h1>
                    <p class="text-white/80 mt-2">{{ $course->short_description }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ ucfirst($course->level) }}</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->duration_total }} jam</span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">{{ $course->total_lessons }} lessons</span>
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center min-w-[150px]">
                    <div class="text-2xl font-bold">{{ round($enrollment->progress) }}%</div>
                    <div class="text-sm">Progress</div>
                    <div class="w-full bg-white/30 rounded-full h-2 mt-2">
                        <div class="bg-white h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content: Sections & Lessons -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">📚 Materi Kursus</h3>
                    </div>
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($sections as $section)
                            <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="w-full flex justify-between items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700 text-left">
                                    <div>
                                        <span
                                            class="font-medium text-gray-800 dark:text-white">{{ $section->title }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ $section->lessons->count() }}
                                            lessons</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="bg-gray-50 dark:bg-gray-700/50">
                                    @foreach ($section->lessons as $lesson)
                                        @php
                                            $completed = \App\Models\LessonCompletion::where('user_id', auth()->id())
                                                ->where('lesson_id', $lesson->id)
                                                ->exists();
                                        @endphp
                                        <a href="{{ route('student.lessons.show', [$course, $lesson]) }}"
                                            class="flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $lesson->id == optional($currentLesson)->id ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}">
                                            <div class="flex items-center space-x-3">
                                                @if ($lesson->type === 'video')
                                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @elseif($lesson->type === 'article')
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                @elseif($lesson->type === 'quiz')
                                                    <svg class="w-5 h-5 text-purple-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300">{{ $lesson->title }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">{{ $lesson->duration }} min</span>
                                                @if ($completed)
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar Informasi -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-3">ℹ️ Tentang Kursus</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Instruktur:</strong> {{ $course->instructor->name }}</p>
                        <p><strong>Level:</strong> {{ ucfirst($course->level) }}</p>
                        <p><strong>Durasi:</strong> {{ $course->duration_total }} jam</p>
                        <p><strong>Total Lesson:</strong> {{ $course->total_lessons }}</p>
                        <p><strong>Siswa:</strong> {{ $course->total_students }}</p>
                        <p><strong>Rating:</strong> {{ number_format($course->average_rating, 1) }} / 5
                            ({{ $course->rating_count }} rating)</p>
                    </div>
                    @if ($course->has_certificate)
                        <div class="mt-3 pt-3 border-t dark:border-gray-700">
                            <div class="flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span class="text-sm">Sertifikat setelah menyelesaikan kursus</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-3">📧 Kontak Instruktur</h3>
                    <div class="flex items-center space-x-2 text-sm">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $course->instructor->email }}</span>
                    </div>
                </div>

                <!-- Next Lesson Button -->
                @if ($currentLesson)
                    <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Lanjutkan Belajar</p>
                                <p class="font-semibold">{{ $currentLesson->title }}</p>
                            </div>
                            <a href="{{ route('student.lessons.show', [$course, $currentLesson]) }}"
                                class="px-4 py-2 bg-white text-primary rounded-lg hover:bg-gray-100 transition font-semibold text-sm">
                                Lanjut →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Review Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">📝 Ulasan Kursus</h3>
                @if ($userReview)
                    <span class="text-sm {{ $userReview->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $userReview->is_approved ? '✓ Review sudah disetujui' : '⏳ Menunggu persetujuan' }}
                    </span>
                @endif
            </div>

            <!-- Rating Summary -->
            <div class="flex items-center space-x-4 mb-6 pb-4 border-b dark:border-gray-700">
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($course->average_rating, 1) }}</div>
                    <div class="flex text-yellow-400 mt-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-sm">{{ $i <= round($course->average_rating) ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $course->rating_count }} ulasan</div>
                </div>
                <div class="flex-1 space-y-1">
                    @for ($star = 5; $star >= 1; $star--)
                        @php
                            $count = App\Models\Review::approved()
                                ->where('course_id', $course->id)
                                ->where('rating', $star)
                                ->count();
                            $percentage = $course->rating_count > 0 ? round(($count / $course->rating_count) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-8">{{ $star }} ★</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="w-12 text-gray-500">{{ $percentage }}%</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Review List -->
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($reviews as $review)
                    <div class="border-b dark:border-gray-700 pb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-semibold text-gray-800 dark:text-white">{{ $review->user->name }}</span>
                                    <div class="flex text-yellow-400 text-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            @if ($review->is_approved)
                                <span class="text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded-full">Disetujui</span>
                            @endif
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada ulasan untuk kursus ini.</p>
                @endforelse
            </div>

            {{ $reviews->links() }}

            <!-- Button Review -->
            @if ($userReview)
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <div class="mb-3">
                        <div class="flex items-center space-x-1 text-yellow-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="text-xl">{{ $i <= $userReview->rating ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $userReview->comment }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('student.reviews.edit', $course->slug) }}"
                            class="text-primary hover:underline text-sm">Edit Review</a>
                    </div>
                </div>
            @elseif($canReview)
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <p class="text-gray-500 text-sm mb-3">Bagikan pengalaman Anda belajar di kursus ini.</p>
                    <a href="{{ route('student.reviews.create', $course->slug) }}"
                        class="inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                        Beri Review
                    </a>
                </div>
            @elseif($enrollment && $enrollment->progress < 50)
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <p class="text-gray-500 text-sm">
                        Selesaikan minimal 50% kursus untuk dapat memberikan review.
                        Progress Anda saat ini: {{ round($enrollment->progress) }}%
                    </p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-primary h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                    </div>
                </div>
            @elseif(!$enrollment)
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <p class="text-gray-500 text-sm">Anda harus terdaftar di kursus ini untuk memberikan review.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function coursePlayer() {
                return {
                    startTime: null,
                    lessonId: {{ $currentLesson->id ?? 'null' }},
                    init() {
                        if (this.lessonId && this.lessonId !== 'null') {
                            this.startTime = Date.now();
                            window.addEventListener('beforeunload', () => {
                                if (this.startTime) {
                                    const timeSpent = Math.floor((Date.now() - this.startTime) / 1000);
                                    navigator.sendBeacon(`/student/lessons/${this.lessonId}/track-time`,
                                        JSON.stringify({
                                            time_spent: timeSpent
                                        })
                                    );
                                }
                            });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
