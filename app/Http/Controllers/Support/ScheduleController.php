<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        return view('support.schedule.index', static::monthData($month, $year));
    }

    /**
     * Dipakai juga oleh widget kalender ringkas di dashboard, supaya logika
     * pengambilan data tidak dobel (lihat Editor\CalendarController::monthData).
     */
    public static function monthData(int $month, int $year): array
    {
        $current = Carbon::create($year, $month, 1);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $schedules = SupportSchedule::with('user')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->groupBy(fn ($s) => $s->date->format('Y-m-d'));

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $days[] = [
                'date' => $d->copy(),
                'items' => $schedules->get($d->format('Y-m-d'), collect()),
            ];
        }

        return [
            'days' => $days,
            'current' => $current,
            'startOffset' => $start->dayOfWeekIso - 1,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:jadwal_support,maintenance,meeting,kegiatan',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        SupportSchedule::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(SupportSchedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Jadwal dihapus.');
    }
}
