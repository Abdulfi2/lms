<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Visibilitas pemakaian kupon pada kursus milik instruktur ini (read-only — pengelolaan kupon tetap di admin).
     */
    public function index(Request $request)
    {
        $baseQuery = Enrollment::whereNotNull('coupon_id')
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        $summary = (clone $baseQuery)
            ->with('coupon')
            ->get()
            ->groupBy('coupon_id')
            ->map(function ($group) {
                return [
                    'coupon' => $group->first()->coupon,
                    'times_used' => $group->count(),
                    'total_discount' => $group->sum('discount_amount'),
                ];
            })
            ->sortByDesc('times_used');

        $redemptions = (clone $baseQuery)
            ->with(['coupon', 'course', 'user'])
            ->latest('enrolled_at')
            ->paginate(15);

        return view('instructor.coupons.index', compact('summary', 'redemptions'));
    }
}
