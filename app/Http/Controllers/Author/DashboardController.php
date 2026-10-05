<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Models\VisitorKhusus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $authorId = Auth::id();
        $articleIds = Article::where('user_id', $authorId)->pluck('id');

        $stats = [
            'total' => $articleIds->count(),
            'draft' => Article::where('user_id', $authorId)->where('status', 'draft')->count(),
            'published' => Article::where('user_id', $authorId)->where('status', 'published')->count(),
            'archived' => Article::where('user_id', $authorId)->where('status', 'archived')->count(),
            'views' => Article::where('user_id', $authorId)->sum('views'),
            'comments' => ArticleComment::whereIn('article_id', $articleIds)->count(),
            'likes' => ArticleLike::whereIn('article_id', $articleIds)->count(),
        ];

        $growth = [
            'total' => $this->monthlyGrowth(Article::where('user_id', $authorId), 'created_at'),
            'views' => $this->monthlyGrowth(VisitorKhusus::whereIn('article_id', $articleIds), 'visited_at'),
            'comments' => $this->monthlyGrowth(ArticleComment::whereIn('article_id', $articleIds), 'created_at'),
            'likes' => $this->monthlyGrowth(ArticleLike::whereIn('article_id', $articleIds), 'created_at'),
        ];

        // Statistik Artikel: pengunjung unik & tayangan harian 30 hari terakhir,
        // dari log kunjungan nyata (tabel visitor_khusus), bukan data simulasi.
        $since = now()->subDays(29)->startOfDay();
        $visits = VisitorKhusus::whereIn('article_id', $articleIds)
            ->where('visited_at', '>=', $since)
            ->get(['user_id', 'ip_address', 'visited_at']);

        $chartLabels = [];
        $chartVisitors = [];
        $chartViews = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayKey = $day->format('Y-m-d');
            $dayVisits = $visits->filter(fn ($v) => $v->visited_at->format('Y-m-d') === $dayKey);

            $chartLabels[] = $day->format('d M');
            $chartViews[] = $dayVisits->count();
            $chartVisitors[] = $dayVisits->unique(fn ($v) => $v->user_id ?? $v->ip_address)->count();
        }

        $draftArticles = Article::where('user_id', $authorId)
            ->where('status', 'draft')
            ->latest('updated_at')
            ->limit(3)
            ->get();

        $totalForPercent = max($stats['total'], 1);
        $topCategories = Article::where('user_id', $authorId)
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->category->name ?? 'Tanpa Kategori',
                'count' => $row->total,
                'percent' => round($row->total / $totalForPercent * 100),
            ])
            ->sortByDesc('count')
            ->values();

        $recentComments = ArticleComment::whereIn('article_id', $articleIds)
            ->with(['article', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'icon' => 'comment',
                'text' => $c->user->name . ' mengomentari "' . Str::limit($c->article->title, 30) . '"',
                'time' => $c->created_at,
            ]);

        $recentLikes = ArticleLike::whereIn('article_id', $articleIds)
            ->with(['article', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($l) => [
                'icon' => 'like',
                'text' => $l->user->name . ' menyukai "' . Str::limit($l->article->title, 30) . '"',
                'time' => $l->created_at,
            ]);

        $recentActivity = $recentComments->concat($recentLikes)->sortByDesc('time')->take(6)->values();

        $recentArticles = Article::where('user_id', $authorId)
            ->with('category')
            ->withCount('comments')
            ->latest()
            ->limit(5)
            ->get();

        return view('author.dashboard', compact(
            'stats',
            'growth',
            'chartLabels',
            'chartVisitors',
            'chartViews',
            'draftArticles',
            'topCategories',
            'recentActivity',
            'recentArticles'
        ));
    }

    /**
     * Persentase perubahan jumlah baris bulan ini vs bulan lalu untuk query
     * yang diberikan. Null kalau bulan lalu datanya kosong — supaya tidak
     * menampilkan angka persentase yang menyesatkan (mis. dari 0 ke 3 bukan "+∞%").
     */
    private function monthlyGrowth($query, string $column): ?float
    {
        $thisMonth = (clone $query)
            ->whereBetween($column, [now()->startOfMonth(), now()])
            ->count();

        $lastMonth = (clone $query)
            ->whereBetween($column, [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])
            ->count();

        if ($lastMonth === 0) {
            return null;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100);
    }
}
