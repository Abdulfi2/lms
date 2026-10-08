<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Lihat-saja — tidak ada aksi konfirmasi/refund pembayaran di sini (itu
     * wewenang admin, lihat Admin\EnrollmentController), sesuai permission
     * 'view enrollments' yang di-seed untuk role support.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(15)->withQueryString();

        return view('support.enrollments.index', compact('enrollments'));
    }
}
