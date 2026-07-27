@extends('layouts.app')

@section('title', 'Manajemen Forum')
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', 'Pantau dan berpartisipasi dalam diskusi di kursus Anda.')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Diskusi (Threads)</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalThreads) }}</h3>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Forum Aktif</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $forums->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Forum List -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Daftar Forum Kursus</h3>
            @forelse($forums as $forum)
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded uppercase mr-2">{{ $forum->course->level }}</span>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary transition">{{ $forum->course->title }}</h4>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ Str::limit($forum->description, 100) }}</p>
                        
                        <div class="flex items-center space-x-4 text-xs text-gray-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                {{ $forum->threads_count }} Diskusi
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                {{ $forum->post_count }} Postingan
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('instructor.forums.show', [$forum->course, $forum]) }}" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-primary hover:text-white transition">
                        Buka Forum
                    </a>
                </div>
                
                @if($forum->latestThread)
                <div class="mt-4 pt-4 border-t dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="{{ $forum->latestThread->user->avatar_url }}" class="w-6 h-6 rounded-full mr-2" alt="">
                        <p class="text-[11px] text-gray-500">Terakhir oleh <span class="font-bold text-gray-700 dark:text-gray-300">{{ $forum->latestThread->user->name }}</span></p>
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $forum->latestThread->created_at->diffForHumans() }}</span>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl shadow-sm text-center">
                <p class="text-gray-500">Belum ada forum aktif.</p>
            </div>
            @endforelse
        </div>

        <!-- Recent Threads -->
        <div class="space-y-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Diskusi Terbaru</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y dark:divide-gray-700">
                @foreach($recentThreads as $thread)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <a href="{{ route('instructor.forums.thread.show', [$thread->forum->course, $thread->forum, $thread]) }}" class="block">
                        <p class="text-sm font-bold text-gray-800 dark:text-white line-clamp-1 mb-1">{{ $thread->title }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-primary font-medium">{{ $thread->forum->course->title }}</span>
                            <span class="text-[10px] text-gray-400">{{ $thread->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
