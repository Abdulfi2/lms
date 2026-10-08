@extends('layouts.app')

@section('title', 'Komentar')
@section('page-title', 'Komentar')
@section('page-subtitle', 'Moderasi komentar di seluruh artikel sistem')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="divide-y dark:divide-gray-700">
            @forelse ($comments as $comment)
                <div class="px-6 py-4 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <img src="{{ $comment->user->avatar_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        <div class="min-w-0">
                            <p class="text-sm">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $comment->user->name }}</span>
                                <span class="text-gray-400"> &middot; </span>
                                <a href="{{ route('articles.show', $comment->article->slug) }}" class="text-primary hover:underline">{{ Str::limit($comment->article->title, 50) }}</a>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 break-words">{{ $comment->content }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('editor.comments.destroy', $comment) }}" onsubmit="return confirm('Hapus komentar ini?')" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                    </form>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Belum ada komentar.
                </div>
            @endforelse
        </div>
        @if ($comments->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
