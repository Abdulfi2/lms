<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with('course');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->latest()->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.coupons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil dibuat.');
    }

    public function edit(Coupon $coupon)
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.coupons.edit', compact('coupon', 'courses'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon->id);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil diperbarui.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Kupon berhasil dihapus.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', 'Status kupon berhasil diubah.');
    }

    private function validateCoupon(Request $request, $ignoreId = null)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $ignoreId,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'course_id' => 'nullable|exists:courses,id',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');
        // Input datetime-local yang dikosongkan mengirim '' (bukan absen), dan '' bukan
        // nilai DATETIME yang valid di MySQL — normalisasi ke null di sini.
        $validated['starts_at'] = $validated['starts_at'] ?: null;
        $validated['expires_at'] = $validated['expires_at'] ?: null;

        return $validated;
    }
}
