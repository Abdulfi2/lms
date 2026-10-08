@extends('layouts.app')

@section('title', 'Tinjau Artikel')
@section('page-title', 'Tinjau Artikel')
@section('page-subtitle', 'Pratinjau draft milik ' . $article->author->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ revisionFormOpen: false }">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('editor.articles.index', ['tab' => 'pending']) }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke daftar</a>
        <div class="flex items-center gap-2 flex-wrap">
            @if ($article->status !== 'published')
                <form method="POST" action="{{ route('editor.articles.publish', $article) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Publish</button>
                </form>
            @endif
            @if ($article->status !== 'ready_to_publish' && $article->status !== 'published')
                <form method="POST" action="{{ route('editor.articles.mark-ready', $article) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary/5 transition text-sm">Tandai Siap Terbit</button>
                </form>
            @endif
            @if ($article->status !== 'published')
                <button type="button" @click="revisionFormOpen = !revisionFormOpen" class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Minta Revisi
                </button>
            @endif
            @if ($article->status !== 'archived')
                <form method="POST" action="{{ route('editor.articles.archive', $article) }}" onsubmit="return confirm('Arsipkan artikel ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 border dark:border-gray-600 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Arsipkan</button>
                </form>
            @endif
        </div>
    </div>

    <div x-show="revisionFormOpen" x-cloak class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('editor.articles.request-revision', $article) }}">
            @csrf
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan revisi untuk {{ $article->author->name }}</label>
            <textarea name="revision_notes" rows="3" required maxlength="1000" placeholder="Jelaskan bagian mana yang perlu diperbaiki..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('revision_notes') }}</textarea>
            @error('revision_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" @click="revisionFormOpen = false" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">Kirim Permintaan Revisi</button>
            </div>
        </form>
    </div>

    @if ($article->status === 'revision' && $article->revision_notes)
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-5 text-sm text-red-800 dark:text-red-400">
            <p class="font-medium mb-1">Catatan revisi yang dikirim:</p>
            <p>{{ $article->revision_notes }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if ($article->featured_image)
            <img src="{{ Storage::url($article->featured_image) }}" class="w-full h-64 object-cover">
        @endif
        <div class="p-6 md:p-8">
            <div class="flex items-center gap-3 text-sm text-gray-500 mb-4 flex-wrap">
                <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">{{ $article->category->name ?? 'Umum' }}</span>
                <x-article-status-badge :status="$article->status" />
                <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $article->title }}</h1>
            @if ($article->subtitle)
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-4">{{ $article->subtitle }}</p>
            @endif
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <img src="{{ $article->author->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                <span>{{ $article->author->name }}</span>
                <span>&middot;</span>
                <span>{{ $article->created_at->diffForHumans() }}</span>
            </div>

            @if ($article->excerpt)
                <p class="text-gray-600 dark:text-gray-400 italic mb-6 border-l-4 border-primary/30 pl-4">{{ $article->excerpt }}</p>
            @endif

            <div class="prose dark:prose-invert max-w-none">
                {!! $article->content !!}
            </div>

            @if ($article->tags->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($article->tags as $tag)
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
