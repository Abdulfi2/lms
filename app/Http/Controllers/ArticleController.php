<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc');

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('excerpt', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $articles = $query->paginate(12);
        $categories = ArticleCategory::where('is_active', true)
            ->withCount('articles')
            ->orderBy('order')
            ->get();
        // Prioritaskan artikel yang sengaja ditandai unggulan oleh admin; kalau
        // belum ada yang ditandai, fallback ke artikel terbaru supaya section
        // ini tidak kosong.
        $featuredArticles = Article::published()->where('is_featured', true)->latest()->limit(3)->get();
        if ($featuredArticles->isEmpty()) {
            $featuredArticles = Article::published()->latest()->limit(3)->get();
        }

        return view('articles.index', compact('articles', 'categories', 'featuredArticles'));
    }

    public function show($slug)
    {
        $article = Article::with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $article->increment('views');

        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->published()
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}