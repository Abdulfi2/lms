<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $months = $this->last12Months();

        // 1. Tren pendapatan (12 bulan terakhir)
        $revenueByMonth = Payment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        // 2. Tren pendaftaran (12 bulan terakhir)
        $enrollmentByMonth = Enrollment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        // 3. Tren registrasi user baru (12 bulan terakhir)
        $userByMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        $revenueTrend = [];
        $enrollmentTrend = [];
        $userTrend = [];
        foreach ($months as $key => $label) {
            $revenueTrend[] = (float) ($revenueByMonth[$key] ?? 0);
            $enrollmentTrend[] = (int) ($enrollmentByMonth[$key] ?? 0);
            $userTrend[] = (int) ($userByMonth[$key] ?? 0);
        }
        $monthLabels = array_values($months);

        // 4. Kursus terlaris (berdasarkan pendapatan)
        $topCoursesByRevenue = Course::query()
            ->select('courses.id', 'courses.title')
            ->selectSub(function ($q) {
                $q->from('payments')
                    ->join('enrollments', 'enrollments.id', '=', 'payments.enrollment_id')
                    ->whereColumn('enrollments.course_id', 'courses.id')
                    ->where('payments.status', 'completed')
                    ->selectRaw('COALESCE(SUM(payments.amount), 0)');
            }, 'revenue')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // 5. Kursus terpopuler (berdasarkan jumlah siswa)
        $topCoursesByEnrollment = Course::orderByDesc('total_students')->limit(5)->get(['id', 'title', 'total_students']);

        // 6. Distribusi role user
        $roleDistribution = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('COUNT(*) as total'))
            ->groupBy('roles.name')
            ->pluck('total', 'name');

        // 7. Distribusi status kursus
        $courseStatusDistribution = Course::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // 8. Distribusi status pembayaran enrollment
        $paymentStatusDistribution = Enrollment::select('payment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        // 9. Ringkasan umum
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalEnrollments = Enrollment::count();
        $avgOrderValue = Payment::where('status', 'completed')->avg('amount') ?? 0;
        $overallCompletionRate = $totalEnrollments > 0
            ? round((Enrollment::where('status', 'completed')->count() / $totalEnrollments) * 100)
            : 0;

        return view('admin.analytics.index', compact(
            'monthLabels',
            'revenueTrend',
            'enrollmentTrend',
            'userTrend',
            'topCoursesByRevenue',
            'topCoursesByEnrollment',
            'roleDistribution',
            'courseStatusDistribution',
            'paymentStatusDistribution',
            'totalRevenue',
            'totalEnrollments',
            'avgOrderValue',
            'overallCompletionRate'
        ));
    }

    /**
     * Peta bulan (Y-m => label singkat) untuk 12 bulan terakhir, urut kronologis.
     */
    private function last12Months(): array
    {
        $months = [];
        $cursor = Carbon::now()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $months[$cursor->format('Y-m')] = $cursor->translatedFormat('M Y');
            $cursor->addMonth();
        }

        return $months;
    }
}
