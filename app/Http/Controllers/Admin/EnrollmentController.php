<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Daftar enrollment yang menunggu konfirmasi pembayaran (dan riwayatnya).
     * Belum ada payment gateway di aplikasi ini, jadi konfirmasi dilakukan manual oleh admin.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course'])
            ->where('amount_paid', '>', 0);

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        } else {
            $query->where('payment_status', 'pending');
        }

        $enrollments = $query->latest('enrolled_at')->paginate(15)->withQueryString();

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Tandai enrollment sebagai sudah lunas (konfirmasi pembayaran manual).
     */
    public function markPaid(Enrollment $enrollment)
    {
        if ($enrollment->payment_status === 'paid') {
            return back()->with('info', 'Enrollment ini sudah berstatus lunas.');
        }

        $enrollment->update(['payment_status' => 'paid']);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi. Siswa sudah bisa mengakses kursus.');
    }

    /**
     * Proses refund penuh: batalkan akses siswa & tandai pembayaran terkait (jika ada) sebagai refunded.
     */
    public function refund(Request $request, Enrollment $enrollment)
    {
        if ($enrollment->payment_status !== 'paid') {
            return back()->with('error', 'Hanya enrollment berstatus lunas yang bisa direfund.');
        }

        // Dibatasi 255 karakter karena kolom payments.refund_reason (varchar) yang ikut diperbarui di bawah.
        $request->validate([
            'refund_reason' => 'required|string|max:255',
        ]);

        $enrollment->update([
            'payment_status' => 'refunded',
            'status' => 'cancelled',
            'refund_reason' => $request->input('refund_reason'),
            'refunded_at' => now(),
        ]);

        Payment::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->update([
                'status' => 'refunded',
                'refund_amount' => $enrollment->amount_paid,
                'refund_reason' => $request->input('refund_reason'),
                'refunded_at' => now(),
            ]);

        return back()->with('success', 'Refund berhasil diproses. Akses siswa ke kursus telah dicabut.');
    }
}
