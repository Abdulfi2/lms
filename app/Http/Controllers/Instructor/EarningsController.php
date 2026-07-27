<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    /**
     * Tampilkan laporan pendapatan instruktur.
     */
    public function index()
    {
        $instructorId = Auth::id();

        // 1. Ringkasan Saldo (Completed) — dikurangi refund_amount jika ada refund parsial.
        // Catatan: ini adalah nilai transaksi kotor (belum dipotong komisi platform),
        // karena skema saat ini belum punya kolom komisi/instructor_share.
        $totalEarnings = Payment::whereHas('enrollment.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->where('status', 'completed')->sum(DB::raw('amount - COALESCE(refund_amount, 0)'));

        // 2. Pendapatan Tertunda (Pending/Processing)
        $pendingEarnings = Payment::whereHas('enrollment.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->whereIn('status', ['pending', 'processing'])->sum('amount');

        // 3. Statistik Bulanan (6 bulan terakhir)
        $monthlyEarnings = Payment::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->whereHas('enrollment.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // 4. Riwayat Transaksi Terbaru
        $transactions = Payment::with(['enrollment.course', 'user'])
            ->whereHas('enrollment.course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->latest()
            ->paginate(10);

        // 5. Payout — saldo yang sudah dicairkan admin
        $totalPaidOut = Payout::where('instructor_id', $instructorId)->sum('amount');
        $outstandingBalance = $totalEarnings - $totalPaidOut;
        $lastPayout = Payout::where('instructor_id', $instructorId)->latest('paid_at')->first();
        $payouts = Payout::where('instructor_id', $instructorId)->latest('paid_at')->limit(10)->get();

        return view('instructor.earnings.index', compact(
            'totalEarnings',
            'pendingEarnings',
            'monthlyEarnings',
            'transactions',
            'totalPaidOut',
            'outstandingBalance',
            'lastPayout',
            'payouts'
        ));
    }
}
