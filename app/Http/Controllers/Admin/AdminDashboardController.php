<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PostReport;
use App\Models\Profile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

        $totalCourses = Course::count();
        $newCoursesThisMonth = Course::where('created_at', '>=', now()->startOfMonth())->count();

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $revenueThisMonth = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $totalEnrollments = Enrollment::count();
        $completedEnrollments = Enrollment::where('status', 'completed')->count();
        $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100) : 0;

        // Butuh Perhatian — item yang menunggu tindakan admin
        $pendingInstructorApprovals = Profile::where('profile_type', 'instructor')
            ->where('approval_status', 'pending')
            ->count();
        $pendingPayments = Enrollment::where('payment_status', 'pending')
            ->where('amount_paid', '>', 0)
            ->count();
        $pendingReviews = Review::where('is_approved', false)->count();
        $pendingCourses = Course::where('status', 'pending')->count();
        $pendingPostReports = PostReport::where('status', 'pending')->count();
        $newContactMessages = ContactMessage::where('status', 'new')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();

        // Statistik pendaftaran 6 bulan terakhir
        $enrollmentPerMonth = Enrollment::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        $cursor = now()->subMonths(5)->startOfMonth();
        for ($i = 0; $i < 6; $i++) {
            $key = $cursor->format('Y-m');
            $chartLabels[] = $cursor->translatedFormat('M');
            $match = $enrollmentPerMonth->firstWhere('month', $key);
            $chartData[] = $match->total ?? 0;
            $cursor->addMonth();
        }

        // Kursus terpopuler (berdasarkan jumlah siswa)
        $popularCourses = Course::where('status', 'published')
            ->orderByDesc('total_students')
            ->limit(5)
            ->get(['id', 'title', 'total_students']);
        $maxStudents = max(1, optional($popularCourses->first())->total_students ?? 1);

        // User terbaru
        $recentUsers = User::with('roles')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalCourses',
            'newCoursesThisMonth',
            'totalRevenue',
            'revenueThisMonth',
            'completionRate',
            'pendingInstructorApprovals',
            'pendingPayments',
            'pendingReviews',
            'pendingCourses',
            'pendingPostReports',
            'newContactMessages',
            'failedJobsCount',
            'chartLabels',
            'chartData',
            'popularCourses',
            'maxStudents',
            'recentUsers'
        ));
    }
}
