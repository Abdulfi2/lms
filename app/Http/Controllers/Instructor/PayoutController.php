<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Ajukan permintaan pencairan saldo.
     */
    public function store(Request $request)
    {
        $instructor = auth()->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        if (Payout::where('instructor_id', $instructor->id)->where('status', 'pending')->exists()) {
            return back()->with('error', 'Anda masih memiliki permintaan payout yang sedang menunggu review admin.');
        }

        $outstanding = $this->outstandingBalance($instructor);

        if ($validated['amount'] > $outstanding) {
            return back()->with('error', 'Jumlah melebihi saldo tertunda Anda (Rp ' . number_format($outstanding, 0, ',', '.') . ').');
        }

        Payout::create([
            'instructor_id' => $instructor->id,
            'status' => 'pending',
            'amount' => $validated['amount'],
            'method' => $validated['method'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Permintaan payout berhasil diajukan. Admin akan meninjau permintaan Anda.');
    }

    private function outstandingBalance($instructor)
    {
        $totalEarnings = Payment::whereHas('enrollment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })->where('status', 'completed')->sum(DB::raw('amount - COALESCE(refund_amount, 0)'));

        $paidOrPending = Payout::where('instructor_id', $instructor->id)
            ->whereIn('status', ['paid', 'pending'])
            ->sum('amount');

        return $totalEarnings - $paidOrPending;
    }
}
