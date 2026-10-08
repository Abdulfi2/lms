<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $range = (int) $request->get('range', 6); // bulan

        $byStatus = Ticket::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $byPriority = Ticket::selectRaw('priority, count(*) as total')->groupBy('priority')->pluck('total', 'priority');

        $byCategory = Ticket::selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($row) => ['name' => $row->category->name ?? 'Tanpa Kategori', 'total' => $row->total])
            ->sortByDesc('total')
            ->values();

        $monthLabels = [];
        $monthlyNew = [];
        $monthlyResolved = [];
        for ($i = $range - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->translatedFormat('M Y');
            $monthlyNew[] = Ticket::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $monthlyResolved[] = Ticket::where('status', 'selesai')
                ->whereYear('closed_at', $month->year)
                ->whereMonth('closed_at', $month->month)
                ->count();
        }

        return view('support.reports.index', compact(
            'byStatus',
            'byPriority',
            'byCategory',
            'monthLabels',
            'monthlyNew',
            'monthlyResolved',
            'range'
        ));
    }
}
