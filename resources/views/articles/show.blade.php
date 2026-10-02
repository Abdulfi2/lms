@extends('layouts.client')

@section('title', $article->title)
@section('page-title', $article->title)
@section('page-subtitle', $article->excerpt ?? strip_tags($article->content))

@section('content')
    <div class="max-w-4xl mx-auto">
        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Featured Image -->
            @if ($article->featured_image)
                <div class="w-full h-80 md:h-96 overflow-hidden">
                    <img src="{{ Storage::url($article->featured_image) }}" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Article Header -->
            <div class="p-6 md:p-8 border-b dark:border-gray-700">
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-4 flex-wrap">
                    <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">
                        {{ $article->category->name ?? 'Umum' }}
                    </span>
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ $article->published_at->format('d F Y') }}</span>
                    <span><i class="far fa-clock mr-1"></i> {{ $article->reading_time }}</span>
                    <span><i class="far fa-eye mr-1"></i> {{ number_format($article->views) }} dilihat</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $article->title }}</h1>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $article->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->author->name) . '&background=769826&color=white' }}"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $article->author->name }}</p>
                            <p class="text-sm text-gray-500">Penulis</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="p-6 md:p-8 prose dark:prose-invert max-w-none">
                {!! $article->content !!}
            </div>

            <!-- Article Footer -->
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div class="flex gap-4">
                        <button class="flex items-center gap-2 text-gray-500 hover:text-red-500 transition">
                            <i class="far fa-heart"></i>
                            <span>{{ number_format($article->likes) }}</span>
                        </button>
                        <button class="flex items-center gap-2 text-gray-500 hover:text-primary transition">
                            <i class="far fa-share-alt"></i>
                            <span>Bagikan</span>
                        </button>
                    </div>
                    <button onclick="window.print()" class="text-gray-500 hover:text-primary transition">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        @if ($relatedArticles->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Artikel Terkait</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($relatedArticles as $related)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                            @if ($related->featured_image)
                                <img src="{{ Storage::url($related->featured_image) }}"
                                    class="w-full h-40 object-cover group-hover:scale-105 transition">
                            @endif
                            <div class="p-4">
                                <span class="text-xs text-gray-500">{{ $related->published_at->format('d M Y') }}</span>
                                <a href="{{ route('articles.show', $related->slug) }}">
                                    <h4
                                        class="font-semibold text-gray-900 dark:text-white mt-1 hover:text-primary transition">
                                        {{ Str::limit($related->title, 50) }}
                                    </h4>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
