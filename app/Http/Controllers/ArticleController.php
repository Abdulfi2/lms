<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleLike;
use App\Models\Tag;
use App\Models\VisitorKhusus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'category'])
            ->published();

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('excerpt', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderBy('views', 'desc'),
            'oldest' => $query->orderBy('published_at', 'asc'),
            default => $query->orderBy('published_at', 'desc'),
        };

        // Artikel yang sengaja ditandai unggulan oleh admin tampil sebagai
        // sorotan di atas grid; kalau belum ada yang ditandai, fallback ke
        // artikel terbaru supaya sorotan ini tidak kosong. Dikecualikan dari
        // grid di bawahnya supaya tidak tampil dobel.
        $heroArticle = Article::published()->where('is_featured', true)->latest()->first()
            ?? Article::published()->latest()->first();

        if ($heroArticle && !$request->filled('search') && !$request->filled('category') && !$request->filled('tag')) {
            $query->where('id', '!=', $heroArticle->id);
        } else {
            $heroArticle = null;
        }

        $articles = $query->paginate(8)->withQueryString();

        $categories = ArticleCategory::where('is_active', true)
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->orderBy('order')
            ->get();
        $totalPublished = Article::published()->count();

        $popularArticles = Article::published()->orderBy('views', 'desc')->limit(5)->get();

        $popularTags = Tag::active()->orderBy('usage_count', 'desc')->limit(10)->get();

        return view('articles.index', compact(
            'articles',
            'categories',
            'totalPublished',
            'heroArticle',
            'popularArticles',
            'popularTags',
            'sort'
        ));
    }

    public function show(Request $request, $slug)
    {
        $article = Article::with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $article->increment('views');

        // Catat setiap kunjungan untuk statistik pengunjung/tayangan harian di
        // dashboard author (lihat Author\DashboardController). Tidak di-dedup
        // supaya tetap konsisten dengan counter 'views' di atas yang juga
        // bertambah setiap kali halaman dibuka.
        VisitorKhusus::create([
            'article_id' => $article->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'visited_at' => now(),
        ]);

        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->published()
            ->limit(3)
            ->get();

        $isLiked = $article->isLikedBy(Auth::user());

        $comments = $article->comments()->with('user')->latest()->get();

        return view('articles.show', compact('article', 'relatedArticles', 'isLiked', 'comments'));
    }

    /**
     * Toggle like artikel oleh user yang sedang login. Satu user cuma bisa
     * like sekali per artikel (lihat unique constraint article_likes).
     */
    public function toggleLike(Article $article)
    {
        $existing = ArticleLike::where('article_id', $article->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
            $article->decrement('likes');
            $liked = false;
        } else {
            ArticleLike::create([
                'article_id' => $article->id,
                'user_id' => Auth::id(),
            ]);
            $article->increment('likes');
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes' => $article->fresh()->likes,
        ]);
    }

    /**
     * Catat satu kali share (dari tombol bagikan ke WhatsApp/Facebook/dll
     * atau salin tautan). Tidak butuh login — siapa saja boleh membagikan.
     */
    public function share(Article $article)
    {
        $article->increment('shares');

        return response()->json([
            'success' => true,
            'shares' => $article->fresh()->shares,
        ]);
    }
}