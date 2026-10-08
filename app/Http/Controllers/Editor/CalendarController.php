<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Kalender editorial bulanan — dibangun dari data artikel yang sudah ada
     * (published_at, created_at, updated_at), tanpa tabel deadline terpisah:
     *   - Terbit: artikel published dengan published_at di hari itu.
     *   - Deadline: artikel belum published tapi sudah dijadwalkan (published_at
     *     diisi tanggal masa depan) jatuh di hari itu.
     *   - Review: draft baru masuk (created_at) di hari itu.
     *   - Revisi: artikel diminta revisi (updated_at saat status jadi revision).
     */
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        return view('editor.calendar.index', static::monthData($month, $year));
    }

    /**
     * Dipakai juga oleh widget kalender ringkas di dashboard, supaya logika
     * pengambilan data tidak dobel.
     */
    public static function monthData(int $month, int $year): array
    {
        $current = Carbon::create($year, $month, 1);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $published = Article::where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->get(['id', 'title', 'published_at'])
            ->groupBy(fn ($a) => $a->published_at->format('Y-m-d'));

        $deadlines = Article::whereIn('status', ['draft', 'revision', 'ready_to_publish'])
            ->whereNotNull('published_at')
            ->whereBetween('published_at', [$start, $end])
            ->get(['id', 'title', 'published_at'])
            ->groupBy(fn ($a) => $a->published_at->format('Y-m-d'));

        $submitted = Article::where('status', 'draft')
            ->whereBetween('created_at', [$start, $end])
            ->get(['id', 'title', 'created_at'])
            ->groupBy(fn ($a) => $a->created_at->format('Y-m-d'));

        $revisions = Article::where('status', 'revision')
            ->whereBetween('updated_at', [$start, $end])
            ->get(['id', 'title', 'updated_at'])
            ->groupBy(fn ($a) => $a->updated_at->format('Y-m-d'));

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[] = [
                'date' => $d->copy(),
                'published' => $published->get($key, collect()),
                'deadline' => $deadlines->get($key, collect()),
                'review' => $submitted->get($key, collect()),
                'revision' => $revisions->get($key, collect()),
            ];
        }

        return [
            'days' => $days,
            'current' => $current,
            'startOffset' => $start->dayOfWeekIso - 1, // Senin = 0
        ];
    }
}
