<?php

namespace App\Http\Controllers\Support;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::open()->count(),
            'resolved_today' => Ticket::where('status', 'selesai')->whereDate('closed_at', today())->count(),
            'high_priority' => Ticket::open()->where('priority', 'tinggi')->count(),
        ];

        $growth = [
            'total' => $this->periodGrowth(Ticket::query(), 'created_at', 'month'),
            'open' => $this->periodGrowth(Ticket::open(), 'created_at', 'month'),
            'resolved_today' => $this->periodGrowth(Ticket::where('status', 'selesai'), 'closed_at', 'day'),
            'high_priority' => $this->periodGrowth(Ticket::where('priority', 'tinggi'), 'created_at', 'week'),
        ];

        $performance = [
            'new_30d' => Ticket::where('created_at', '>=', now()->subDays(30))->count(),
            'resolved_30d' => Ticket::where('status', 'selesai')->where('closed_at', '>=', now()->subDays(30))->count(),
        ];
        $performanceGrowth = [
            'new_30d' => $this->periodGrowth(Ticket::query(), 'created_at', 'month'),
            'resolved_30d' => $this->periodGrowth(Ticket::where('status', 'selesai'), 'closed_at', 'month'),
        ];

        // Tren 30 hari terakhir: tiket baru & selesai dari data riil, "terbuka"
        // dihitung kumulatif (baru - selesai) per hari.
        $since = now()->subDays(29)->startOfDay();
        $created = Ticket::where('created_at', '>=', $since)->get(['created_at']);
        $resolved = Ticket::where('status', 'selesai')->where('closed_at', '>=', $since)->get(['closed_at']);

        $chartLabels = [];
        $chartNew = [];
        $chartResolved = [];
        $chartOpen = [];
        $runningOpen = Ticket::where('created_at', '<', $since)
            ->where(function ($q) use ($since) {
                $q->where('status', '!=', 'selesai')->orWhere('closed_at', '>=', $since);
            })->count();

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayKey = $day->format('Y-m-d');
            $newCount = $created->filter(fn ($t) => $t->created_at->format('Y-m-d') === $dayKey)->count();
            $resolvedCount = $resolved->filter(fn ($t) => $t->closed_at->format('Y-m-d') === $dayKey)->count();
            $runningOpen += $newCount - $resolvedCount;

            $chartLabels[] = $day->format('d M');
            $chartNew[] = $newCount;
            $chartResolved[] = $resolvedCount;
            $chartOpen[] = max($runningOpen, 0);
        }

        $topCategories = Ticket::selectRaw('category_id, count(*) as total')
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

        $recentTickets = Ticket::with(['user', 'category'])->withCount('replies')->latest()->limit(5)->get();

        $priorities = [];
        if ($stats['open'] > 0) {
            $priorities[] = [
                'label' => 'Tangani ' . $stats['open'] . ' tiket dalam antrian',
                'detail' => $stats['open'] . ' tiket menunggu tindak lanjut',
                'level' => 'Tinggi',
                'url' => route('support.tickets.index', ['tab' => 'pending']),
            ];
        }
        if ($stats['high_priority'] > 0) {
            $priorities[] = [
                'label' => 'Tiket prioritas tinggi',
                'detail' => $stats['high_priority'] . ' tiket butuh perhatian segera',
                'level' => 'Tinggi',
                'url' => route('support.tickets.index', ['tab' => 'all', 'priority' => 'tinggi']),
            ];
        }
        $newCount = Ticket::where('status', 'baru')->count();
        if ($newCount > 0) {
            $priorities[] = [
                'label' => 'Tiket baru belum direspon',
                'detail' => $newCount . ' tiket menunggu balasan pertama',
                'level' => 'Sedang',
                'url' => route('support.tickets.index', ['tab' => 'all', 'status' => 'baru']),
            ];
        }

        $recentLogs = ActivityLog::where('table_name', 'tickets')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($log) => [
                'text' => $log->description,
                'time' => $log->created_at,
            ]);

        $calendar = ScheduleController::monthData((int) now()->month, (int) now()->year);

        return view('support.dashboard', compact(
            'stats',
            'growth',
            'performance',
            'performanceGrowth',
            'chartLabels',
            'chartNew',
            'chartResolved',
            'chartOpen',
            'topCategories',
            'recentTickets',
            'priorities',
            'recentLogs',
            'calendar'
        ));
    }

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
}
