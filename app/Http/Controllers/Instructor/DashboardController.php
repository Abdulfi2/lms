<?php
// app/Http/Controllers/Instructor/DashboardController.php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $instructorId = auth()->id();

        // Total courses milik instructor
        $totalCourses = Course::where('instructor_id', $instructorId)->count();
        $publishedCourses = Course::where('instructor_id', $instructorId)->where('status', 'published')->count();
        $draftCourses = Course::where('instructor_id', $instructorId)->where('status', 'draft')->count();

        // Total students terdaftar di kursus instructor (unique)
        $totalStudents = Enrollment::whereHas('course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->distinct('user_id')->count('user_id');

        // Total pendapatan (dari payment yang sudah completed)
        // Asumsi: payment terkait dengan enrollment, dan enrollment terkait course instructor
        $totalRevenue = Payment::whereHas('enrollment.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->where('status', 'completed')->sum('amount');

        // Rata-rata rating dari semua kursus instructor (jika ada)
        $avgRating = Course::where('instructor_id', $instructorId)->avg('average_rating');
        $avgRating = round($avgRating, 1);

        // 5 kursus terbaru
        $recentCourses = Course::where('instructor_id', $instructorId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 5 kursus dengan siswa terbanyak
        $topCourses = Course::where('instructor_id', $instructorId)
            ->orderBy('total_students', 'desc')
            ->limit(5)
            ->get();

        // Pendapatan per bulan (6 bulan terakhir) untuk chart
        $revenuePerMonth = Payment::select(
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

        $chartLabels = [];
        $chartData = [];
        foreach ($revenuePerMonth as $item) {
            $chartLabels[] = $item->month;
            $chartData[] = $item->total;
        }

        // Aktivitas terbaru: enrollments terbaru di kursus instructor
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Submission yang menunggu dinilai — ini paling time-sensitive bagi instruktur,
        // jadi ditonjolkan di dashboard utama, bukan cuma di sidebar.
        $pendingGradingCount = Submission::whereHas('assignment.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->where('status', 'submitted')->count();

        $pendingSubmissions = Submission::with(['assignment.course', 'student'])
            ->whereHas('assignment.course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return view('instructor.dashboard', compact(
            'totalCourses',
            'publishedCourses',
            'draftCourses',
            'totalStudents',
            'totalRevenue',
            'avgRating',
            'recentCourses',
            'topCourses',
            'chartLabels',
            'chartData',
            'recentEnrollments',
            'pendingGradingCount',
            'pendingSubmissions'
        ));
    }
}