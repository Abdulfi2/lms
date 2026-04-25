<?php
// app/Http/Controllers/Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get enrollments with course
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->filter(fn($e) => $e->progress >= 100)->count();
        $totalProgress = $enrollments->avg('progress') ?? 0;
        $certificates = $user->certificates()->count();

        // Ambil 5 sertifikat terbaru (jika ada)
        $latestCertificates = $user->certificates()->latest()->take(5)->get();

        // Recommended courses (exclude enrolled ones)
        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        $recommendedCourses = Course::published()
            ->whereNotIn('id', $enrolledCourseIds)
            ->latest()
            ->take(4)
            ->get();

        return view('student.dashboard', compact(
            'enrollments',
            'totalCourses',
            'completedCourses',
            'totalProgress',
            'certificates',
            'latestCertificates',
            'recommendedCourses'
        ));
    }

    public function myCourses()
    {
        $enrollments = Enrollment::with('course')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('student.my-courses', compact('enrollments'));
    }
}