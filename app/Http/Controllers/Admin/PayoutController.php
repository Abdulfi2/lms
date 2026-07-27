<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    /**
     * Ringkasan saldo tiap instruktur: total pendapatan kotor dari transaksi selesai
     * dikurangi total payout yang sudah dicairkan.
     */
    public function index()
    {
        $instructors = User::role('instructor')
            ->select('users.*')
            ->selectSub(function ($query) {
                $query->from('payments')
                    ->join('enrollments', 'enrollments.id', '=', 'payments.enrollment_id')
                    ->join('courses', 'courses.id', '=', 'enrollments.course_id')
                    ->whereColumn('courses.instructor_id', 'users.id')
                    ->where('payments.status', 'completed')
                    ->selectRaw('COALESCE(SUM(payments.amount - COALESCE(payments.refund_amount, 0)), 0)');
            }, 'total_earnings')
            ->selectSub(function ($query) {
                $query->from('payouts')
                    ->whereColumn('payouts.instructor_id', 'users.id')
                    ->selectRaw('COALESCE(SUM(payouts.amount), 0)');
            }, 'total_paid_out')
            ->orderByDesc('total_earnings')
            ->paginate(15);

        return view('admin.payouts.index', compact('instructors'));
    }

    /**
     * Detail saldo & riwayat payout satu instruktur, plus form untuk mencatat payout baru.
     */
    public function show(User $instructor)
    {
        abort_unless($instructor->hasRole('instructor'), 404);

        $totalEarnings = $this->totalEarnings($instructor);
        $totalPaidOut = Payout::where('instructor_id', $instructor->id)->sum('amount');
        $outstanding = $totalEarnings - $totalPaidOut;

        $payouts = Payout::where('instructor_id', $instructor->id)
            ->with('processedBy')
            ->latest('paid_at')
            ->paginate(10);

        return view('admin.payouts.show', compact('instructor', 'totalEarnings', 'totalPaidOut', 'outstanding', 'payouts'));
    }

    /**
     * Catat payout baru untuk seorang instruktur (menandai sebagian/seluruh saldo sudah dicairkan).
     */
    public function store(Request $request, User $instructor)
    {
        abort_unless($instructor->hasRole('instructor'), 404);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'method' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        $outstanding = $this->totalEarnings($instructor) - Payout::where('instructor_id', $instructor->id)->sum('amount');

        if ($validated['amount'] > $outstanding) {
            return back()->withErrors(['amount' => 'Jumlah payout melebihi saldo tertunda instruktur ini (Rp ' . number_format($outstanding, 0, ',', '.') . ').'])->withInput();
        }

        Payout::create([
            'instructor_id' => $instructor->id,
            'processed_by' => auth()->id(),
            'amount' => $validated['amount'],
            'period_start' => $validated['period_start'] ?? null,
            'period_end' => $validated['period_end'] ?? null,
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'note' => $validated['note'] ?? null,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Payout berhasil dicatat. Saldo instruktur diperbarui.');
    }

    /**
     * Hapus catatan payout yang salah input.
     */
    public function destroy(Payout $payout)
    {
        $instructorId = $payout->instructor_id;
        $payout->delete();

        return redirect()->route('admin.payouts.show', $instructorId)->with('success', 'Catatan payout dihapus.');
    }

    private function totalEarnings(User $instructor)
    {
        return \App\Models\Payment::whereHas('enrollment.course', function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })->where('status', 'completed')->sum(\Illuminate\Support\Facades\DB::raw('amount - COALESCE(refund_amount, 0)'));
    }
}
