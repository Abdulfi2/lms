@extends('layouts.app')

@section('title', 'Progress Siswa')
@section('page-title', 'Progress Siswa')
@section('page-subtitle', $user->name . ' - ' . $course->title)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Summary Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                        class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-gray-500">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500">Terdaftar: {{ $enrollment->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="text-center bg-gray-100 dark:bg-gray-700 rounded-lg p-4 min-w-[150px]">
                    <div class="text-3xl font-bold text-primary">{{ $progress }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Progress Kursus</div>
                    <div class="w-full bg-gray-300 rounded-full h-2 mt-2">
                        <div class="bg-primary h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $completedCount }} dari {{ $totalLessons }} lesson selesai
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Lesson dengan Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">Progress per Lesson</h3>
            </div>
            <div class="divide-y dark:divide-gray-700">
                @foreach ($lessons as $lesson)
                    <div class="px-6 py-3 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            @if (in_array($lesson->id, $completedLessonIds))
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $lesson->title }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($lesson->type) }} • {{ $lesson->duration }}
                                    menit</div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            @if (in_array($lesson->id, $completedLessonIds))
                                Selesai
                            @else
                                Belum selesai
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('instructor.students.index') }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100">Kembali ke Daftar Siswa</a>
        </div>
    </div>
@endsection
