@extends('layouts.app')

@section('title', 'Kursus Saya')
@section('page-title', 'Kursus Saya')
@section('page-subtitle', 'Kelola kursus yang Anda buat')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('instructor.courses.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary font-medium shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kursus Baru
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/60x40/3B82F6/white?text=Course' }}"
                                            class="w-16 h-10 object-cover rounded">
                                        <div>
                                            <div class="font-medium">{{ $course->title }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ Str::limit($course->short_description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $course->formatted_price }}</td>
                                <td class="px-6 py-4 text-xs">{{ $course->total_students }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$course->status" />
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $course->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        <a href="{{ route('instructor.courses.sections.index', $course) }}"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400"
                                            title="Kelola Section & Lesson">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                            <span class="hidden lg:inline">Materi</span>
                                        </a>
                                        <a href="{{ route('instructor.courses.quizzes.index', $course) }}"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-900/20 dark:text-purple-400"
                                            title="Kelola Quiz">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0"
                                                viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M13 18H11V16H13V18M13 15H11C11 11.75 14 12 14 10C14 8.9 13.1 8 12 8C10.9 8 10 8.9 10 10H8C8 7.79 9.79 6 12 6C14.21 6 16 7.79 16 10C16 12.5 13 12.75 13 15M22 12C22 17.18 18.05 21.45 13 21.95V19.94C16.95 19.45 20 16.08 20 12C20 7.92 16.95 4.55 13 4.06V2.05C18.05 2.55 22 6.82 22 12M11 2.05V4.06C9.54 4.24 8.2 4.82 7.09 5.68L5.67 4.26C7.15 3.05 9 2.25 11 2.05M4.06 11H2.05C2.25 9 3.05 7.15 4.26 5.67L5.68 7.1C4.82 8.2 4.24 9.54 4.06 11M11 19.94V21.95C9 21.75 7.15 20.96 5.67 19.74L7.09 18.32C8.2 19.18 9.54 19.76 11 19.94M2.05 13H4.06C4.24 14.46 4.82 15.8 5.68 16.91L4.26 18.33C3.05 16.85 2.25 15 2.05 13Z" />
                                            </svg>
                                            <span class="hidden lg:inline">Quiz</span>
                                        </a>
                                        <a href="{{ route('instructor.courses.edit', $course) }}"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400"
                                            title="Edit Kursus">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="hidden lg:inline">Edit</span>
                                        </a>
                                        <a href="{{ route('instructor.courses.preview', $course) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
                                            title="Lihat sebagai calon siswa">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="hidden lg:inline">Preview</span>
                                        </a>
                                        <form action="{{ route('instructor.courses.duplicate', $course) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Duplikasi kursus ini beserta section & lesson-nya sebagai draft baru?')">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400"
                                                title="Duplikasi Kursus">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                <span class="hidden lg:inline">Duplikasi</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Yakin hapus kursus ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400"
                                                title="Hapus Kursus">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden lg:inline">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-gray-600 dark:text-gray-300 font-medium">Anda belum punya kursus</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "+ Buat Kursus Baru" di atas untuk memulai. Jangan khawatir, prosesnya dipandu langkah demi langkah.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
@endsection
