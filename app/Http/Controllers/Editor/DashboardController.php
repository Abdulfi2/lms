<?php

namespace App\Http\Controllers\Editor;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $editorId = Auth::id();

        $stats = [
            'pending' => Article::where('status', 'draft')->where('user_id', '!=', $editorId)->count(),
            'revision' => Article::where('status', 'revision')->count(),
            'published_today' => Article::where('status', 'published')->whereDate('published_at', today())->count(),
            'active_writers' => User::role('author')
                ->whereHas('articles', fn ($q) => $q->where('updated_at', '>=', now()->subDays(30)))
                ->count(),
        ];

        $growth = [
            'pending' => $this->periodGrowth(
                Article::where('user_id', '!=', $editorId),
                'created_at',
                'week'
            ),
            'revision' => $this->periodGrowth(
                ActivityLog::where('action', 'request_revision'),
                'created_at',
                'week'
            ),
            'published_today' => $this->periodGrowth(
                Article::where('status', 'published'),
                'published_at',
                'day'
            ),
            'active_writers' => $this->activeWritersGrowth(),
        ];

        // Antrian Review Artikel: semua artikel yang masih butuh tindakan editorial.
        $queueQuery = Article::with(['author', 'category'])->whereIn('status', ['draft', 'revision', 'ready_to_publish']);

        if ($request->filled('search')) {
            $queueQuery->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('author', fn ($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $queueQuery->where('status', $request->status);
        }

        $queueArticles = $queueQuery->orderByDesc('created_at')->paginate(5)->withQueryString();

        // Statistik Editorial: aktivitas review & publish harian 30 hari terakhir,
        // dari log aktivitas nyata (activity_logs), bukan data simulasi.
        $since = now()->subDays(29)->startOfDay();
        $logs = ActivityLog::whereIn('action', ['publish', 'request_revision', 'mark_ready'])
            ->where('created_at', '>=', $since)
            ->get(['action', 'created_at']);

        $chartLabels = [];
        $chartReviewed = [];
        $chartPublished = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayKey = $day->format('Y-m-d');
            $dayLogs = $logs->filter(fn ($l) => $l->created_at->format('Y-m-d') === $dayKey);

            $chartLabels[] = $day->format('d M');
            $chartReviewed[] = $dayLogs->count();
            $chartPublished[] = $dayLogs->where('action', 'publish')->count();
        }

        $topCategories = Article::whereNotIn('status', ['archived'])
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->category->name ?? 'Tanpa Kategori',
                'count' => $row->total,
            ])
            ->sortByDesc('count')
            ->values();
        $topCategoriesTotal = max($topCategories->sum('count'), 1);
        $topCategories = $topCategories->map(fn ($c) => $c + ['percent' => round($c['count'] / $topCategoriesTotal * 100)]);

        $priorities = [
            [
                'label' => 'Review ' . $stats['pending'] . ' artikel dalam antrian',
                'detail' => $stats['pending'] . ' artikel menunggu review',
                'level' => 'Tinggi',
                'url' => route('editor.articles.index', ['tab' => 'pending']),
            ],
        ];
        $needsRecheck = Article::where('status', 'draft')->whereNotNull('revision_notes')->count();
        if ($needsRecheck > 0) {
            $priorities[] = [
                'label' => 'Selesaikan pengecekan revisi',
                'detail' => $needsRecheck . ' artikel dikirim ulang setelah revisi',
                'level' => 'Tinggi',
                'url' => route('editor.articles.index', ['tab' => 'all', 'status' => 'draft']),
            ];
        }
        if ($stats['published_today'] >= 0) {
            $readyCount = Article::where('status', 'ready_to_publish')->count();
            if ($readyCount > 0) {
                $priorities[] = [
                    'label' => 'Finalisasi artikel untuk terbit',
                    'detail' => $readyCount . ' artikel siap terbit',
                    'level' => 'Sedang',
                    'url' => route('editor.articles.index', ['tab' => 'all', 'status' => 'ready_to_publish']),
                ];
            }
        }
        $newComments = ArticleComment::where('created_at', '>=', now()->subDay())->count();
        if ($newComments > 0) {
            $priorities[] = [
                'label' => 'Cek komentar dan feedback',
                'detail' => $newComments . ' komentar baru dalam 24 jam',
                'level' => 'Rendah',
                'url' => route('editor.comments.index'),
            ];
        }

        $recentLogs = ActivityLog::whereIn('action', ['publish', 'archive', 'request_revision', 'mark_ready'])
            ->where('user_id', $editorId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'icon' => $log->action,
                'text' => $log->description,
                'time' => $log->created_at,
            ]);

        $recentComments = ArticleComment::where('user_id', $editorId)
            ->with('article')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'icon' => 'comment',
                'text' => Auth::user()->name . ' memberikan komentar di "' . Str::limit($c->article->title, 30) . '"',
                'time' => $c->created_at,
            ]);

        $recentActivity = $recentLogs->concat($recentComments)->sortByDesc('time')->take(6)->values();

        $calendar = CalendarController::monthData((int) now()->month, (int) now()->year);

        return view('editor.dashboard', compact(
            'stats',
            'growth',
            'queueArticles',
            'chartLabels',
            'chartReviewed',
            'chartPublished',
            'topCategories',
            'priorities',
            'recentActivity',
            'calendar'
        ));
    }

    /**
     * Persentase perubahan jumlah baris periode ini vs periode sebelumnya.
     * Null kalau periode sebelumnya kosong — supaya tidak menampilkan
     * persentase yang menyesatkan.
     */
    private function periodGrowth($query, string $column, string $period): ?float
    {
        [$startThis, $startLast, $endLast] = match ($period) {
            'day' => [now()->startOfDay(), now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            default => [now()->startOfMonth(), now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
        };

        $thisCount = (clone $query)->where($column, '>=', $startThis)->count();
        $lastCount = (clone $query)->whereBetween($column, [$startLast, $endLast])->count();

        if ($lastCount === 0) {
            return null;
        }

        return round((($thisCount - $lastCount) / $lastCount) * 100);
    }

    private function activeWritersGrowth(): ?float
    {
        $thisMonth = User::role('author')
            ->whereHas('articles', fn ($q) => $q->where('updated_at', '>=', now()->startOfMonth()))
            ->count();

        $lastMonth = User::role('author')
            ->whereHas('articles', fn ($q) => $q->whereBetween('updated_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
            ]))
            ->count();

        if ($lastMonth === 0) {
            return null;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100);
    }
}
