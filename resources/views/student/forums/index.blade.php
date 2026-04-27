@extends('layouts.app')

@section('title', 'Forum Diskusi')
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', 'Diskusikan materi kursus dengan sesama siswa dan instruktur')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Forum</p>
                <p class="text-2xl font-bold">{{ $forums->total() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Thread</p>
                <p class="text-2xl font-bold">{{ $totalThreads }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Post</p>
                <p class="text-2xl font-bold">{{ $totalPosts }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Post Saya</p>
                <p class="text-2xl font-bold">{{ $myPosts }}</p>
            </div>
        </div>

        <!-- Forum List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($forums as $forum)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <a href="{{ route('student.forums.show', [$forum->course, $forum]) }}"
                                    class="text-xl font-semibold text-gray-800 dark:text-white hover:text-primary transition">
                                    {{ $forum->title }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">{{ $forum->description }}</p>
                                <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        {{ $forum->thread_count }} thread
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        {{ $forum->post_count }} post
                                    </span>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Aktif</span>
                        </div>

                        @if ($forum->latestThread)
                            <div class="mt-4 pt-3 border-t dark:border-gray-700 text-xs text-gray-500">
                                <span>Diskusi terakhir: </span>
                                <a href="#" class="text-primary hover:underline font-medium">
                                    {{ Str::limit($forum->latestThread->title, 50) }}
                                </a>
                                <span> oleh {{ $forum->latestThread->user->name }}</span>
                                <span class="ml-1">{{ $forum->latestThread->created_at->diffForHumans() }}</span>
                            </div>
                        @endif

                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('student.forums.thread.create', [$forum->course, $forum]) }}"
                                class="text-sm text-primary hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Thread Baru
                            </a>
                            <a href="{{ route('student.forums.show', [$forum->course, $forum]) }}"
                                class="text-primary hover:underline text-sm">
                                Lihat Semua Thread →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-gray-500">Belum ada forum diskusi yang tersedia.</p>
                    <p class="text-sm text-gray-400 mt-1">Forum akan muncul setelah instruktur membuat forum diskusi di
                        kursus yang Anda ikuti.</p>
                </div>
            @endforelse
        </div>

        {{ $forums->links() }}
    </div>
@endsection
