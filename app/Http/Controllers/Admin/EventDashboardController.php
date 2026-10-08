<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventDashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = in_array((int) $request->query('range'), [7, 30, 90], true) ? (int) $request->query('range') : 30;

        $totalEvents = $this->scopedEvents()->count();
        $activeEvents = $this->scopedEvents()->where('status', 'published')
            ->where(fn ($q) => $q->where('start_time', '>', now())->orWhere('end_time', '>=', now()))
            ->count();
        $totalParticipants = $this->scopedRegistrations()->where('status', '!=', 'cancelled')->count();

        $stats = [
            'total_events' => $totalEvents,
            'active_events' => $activeEvents,
            'participants' => $totalParticipants,
            'attendance' => $this->attendanceRate(),
        ];

        // Tren "dari bulan lalu". Event Aktif sengaja tanpa tren: statusnya bergantung pada
        // waktu sekarang, jadi tidak ada angka bulan lalu yang sebanding.
        $growth = [
            'total_events' => $this->monthlyGrowth($this->scopedEvents(), 'created_at'),
            'active_events' => null,
            'participants' => $this->monthlyGrowth($this->scopedRegistrations()->where('status', '!=', 'cancelled'), 'created_at'),
            'attendance' => $this->attendanceGrowth(),
        ];

        return view('admin.events.dashboard', [
            'stats' => $stats,
            'growth' => $growth,
            'range' => $range,
            'performance' => $this->performance($range),
            'recentEvents' => $this->scopedEvents()->withCount(['registrations as participants_count' => fn ($q) => $q->where('status', '!=', 'cancelled')])
                ->latest()->limit(5)->get(),
            'agenda' => $this->agenda(),
            'calendar' => $this->calendar($request->query('month')),
            'categories' => $this->categories($totalEvents),
            'activities' => $this->activities(),
        ]);
    }

    /**
     * Event manager hanya melihat event miliknya; admin melihat semua.
     */
    private function scopedEvents()
    {
        $query = Event::query();
        $user = auth()->user();

        if ($user->hasRole('event_manager') && !$user->hasRole('admin')) {
            $query->where('organizer_id', $user->id);
        }

        return $query;
    }

    private function scopedRegistrations()
    {
        return EventRegistration::whereIn('event_id', $this->scopedEvents()->select('id'));
    }

    /**
     * Pendaftaran vs kehadiran per hari, plus ringkasan hadir / tidak hadir.
     * Hadir dan tidak hadir dihitung dari event yang sudah berlangsung dalam rentang,
     * "tidak hadir" = terdaftar (confirmed) tapi tidak check-in.
     */
    private function performance(int $range): array
    {
        $start = now()->subDays($range - 1)->startOfDay();

        $registered = $this->scopedRegistrations()
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')->groupBy('d')->pluck('c', 'd');

        $attended = $this->scopedRegistrations()
            ->where('status', 'attended')
            ->where('checked_in_at', '>=', $start)
            ->selectRaw('DATE(checked_in_at) as d, COUNT(*) as c')->groupBy('d')->pluck('c', 'd');

        $labels = $registeredSeries = $attendedSeries = [];
        for ($i = $range - 1; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->translatedFormat('j M');
            $registeredSeries[] = (int) ($registered[$key] ?? 0);
            $attendedSeries[] = (int) ($attended[$key] ?? 0);
        }

        $pastInRange = fn ($q) => $q->whereBetween('start_time', [$start, now()]);
        $hadir = $this->scopedRegistrations()->where('status', 'attended')->whereHas('event', $pastInRange)->count();
        $tidakHadir = $this->scopedRegistrations()->where('status', 'confirmed')->whereHas('event', $pastInRange)->count();

        return [
            'labels' => $labels,
            'registered' => $registeredSeries,
            'attended' => $attendedSeries,
            'summary' => [
                'registered' => array_sum($registeredSeries),
                'attended' => $hadir,
                'absent' => $tidakHadir,
                'rate' => ($hadir + $tidakHadir) > 0 ? (int) round($hadir / ($hadir + $tidakHadir) * 100) : null,
            ],
        ];
    }

    /**
     * Agenda hari ini: event yang mulai hari ini (atau 3 event terdekat kalau kosong),
     * ditambah event yang masih punya pendaftaran menunggu konfirmasi.
     */
    private function agenda(): array
    {
        $items = [];

        $today = $this->scopedEvents()->where('status', 'published')
            ->whereBetween('start_time', [now()->startOfDay(), now()->endOfDay()])
            ->orderBy('start_time')->get();

        $events = $today->isNotEmpty() ? $today : $this->scopedEvents()->where('status', 'published')
            ->where('start_time', '>', now())->orderBy('start_time')->limit(3)->get();

        foreach ($events as $event) {
            $time = $event->start_time->format('H:i') . ($event->end_time ? ' - ' . $event->end_time->format('H:i') : '');
            $items[] = [
                'title' => $event->title,
                'meta' => $today->isNotEmpty() ? $time : $event->start_time->translatedFormat('j M Y') . ', ' . $time,
                'done' => $event->lifecycle_status === 'finished',
                'url' => route('admin.events.show', $event),
            ];
        }

        $pending = $this->scopedRegistrations()->where('status', 'pending')
            ->selectRaw('event_id, COUNT(*) as total')->groupBy('event_id')->orderByDesc('total')->limit(3)->get();
        $pendingEvents = Event::whereIn('id', $pending->pluck('event_id'))->get()->keyBy('id');

        foreach ($pending as $row) {
            $event = $pendingEvents[$row->event_id] ?? null;
            if (!$event) {
                continue;
            }
            $items[] = [
                'title' => $row->total . ' pendaftaran menunggu konfirmasi',
                'meta' => Str::limit($event->title, 38),
                'done' => false,
                'url' => route('admin.events.registrations', $event),
            ];
        }

        return $items;
    }

    /**
     * Grid kalender bulanan (Senin sebagai hari pertama) dengan penanda event per hari.
     */
    private function calendar(?string $month): array
    {
        $current = ($month && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month))
            ? Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfDay()
            : now()->startOfMonth();

        $events = $this->scopedEvents()->where('status', '!=', 'cancelled')
            ->whereBetween('start_time', [$current->copy()->startOfMonth(), $current->copy()->endOfMonth()])
            ->orderBy('start_time')->get()
            ->groupBy(fn ($e) => $e->start_time->format('Y-m-d'));

        $cursor = $current->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $current->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        while ($cursor <= $end) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->format('Y-m-d');
                $dayEvents = $events[$key] ?? collect();
                $statuses = $dayEvents->map->lifecycle_status;

                // Prioritas warna: hari ini > akan datang > selesai > draft.
                $dot = null;
                if ($dayEvents->isNotEmpty()) {
                    $dot = $cursor->isToday() ? 'today'
                        : ($statuses->contains('upcoming') ? 'upcoming'
                        : ($statuses->contains('finished') ? 'finished' : 'event'));
                }

                $week[] = [
                    'day' => $cursor->day,
                    'in_month' => $cursor->month === $current->month,
                    'is_today' => $cursor->isToday(),
                    'dot' => $dot,
                    'title' => $dayEvents->pluck('title')->implode(', '),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return [
            'label' => $current->translatedFormat('F Y'),
            'prev' => $current->copy()->subMonth()->format('Y-m'),
            'next' => $current->copy()->addMonth()->format('Y-m'),
            'weeks' => $weeks,
        ];
    }

    private function categories(int $totalEvents)
    {
        return $this->scopedEvents()->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')->orderByDesc('total')->get()
            ->map(fn ($row) => [
                'name' => Event::CATEGORY_LABELS[$row->category] ?? Str::headline($row->category),
                'count' => $row->total,
                'percent' => $totalEvents > 0 ? (int) round($row->total / $totalEvents * 100) : 0,
            ]);
    }

    private function activities()
    {
        $registrations = $this->scopedRegistrations()->with('event:id,title')->latest()->limit(6)->get()
            ->map(fn ($r) => [
                'icon' => 'user',
                'text' => $r->name . ' mendaftar event "' . Str::limit($r->event->title ?? '-', 40) . '"',
                'time' => $r->created_at,
            ]);

        $created = $this->scopedEvents()->latest()->limit(3)->get()
            ->map(fn ($e) => [
                'icon' => 'event',
                'text' => 'Event "' . Str::limit($e->title, 40) . '" dibuat',
                'time' => $e->created_at,
            ]);

        return $registrations->concat($created)->sortByDesc('time')->take(6)->values();
    }

    /**
     * Tingkat kehadiran (%) = hadir / (hadir + confirmed) pada event yang sudah dimulai.
     * Dibatasi ke event yang mulai dalam rentang bila $from dan $to diberikan.
     */
    private function attendanceRate(?Carbon $from = null, ?Carbon $to = null): ?int
    {
        $query = $this->scopedRegistrations()
            ->whereIn('status', ['confirmed', 'attended'])
            ->whereHas('event', function ($q) use ($from, $to) {
                $q->where('start_time', '<', now());
                if ($from && $to) {
                    $q->whereBetween('start_time', [$from, $to]);
                }
            });

        $total = (clone $query)->count();

        return $total > 0 ? (int) round((clone $query)->where('status', 'attended')->count() / $total * 100) : null;
    }

    /**
     * Selisih poin persentase kehadiran bulan ini vs bulan lalu; null kalau salah satunya kosong.
     */
    private function attendanceGrowth(): ?int
    {
        $thisMonth = $this->attendanceRate(now()->startOfMonth(), now());
        $lastMonth = $this->attendanceRate(now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth());

        return ($thisMonth === null || $lastMonth === null) ? null : $thisMonth - $lastMonth;
    }

    /**
     * Persentase perubahan jumlah baris bulan ini vs bulan lalu. Null kalau bulan lalu kosong,
     * supaya tidak menampilkan persentase menyesatkan (mis. 0 ke 3 bukan "+∞%").
     */
    private function monthlyGrowth($query, string $column): ?int
    {
        $thisMonth = (clone $query)->whereBetween($column, [now()->startOfMonth(), now()])->count();
        $lastMonth = (clone $query)->whereBetween($column, [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->count();

        return $lastMonth === 0 ? null : (int) round((($thisMonth - $lastMonth) / $lastMonth) * 100);
    }
}
