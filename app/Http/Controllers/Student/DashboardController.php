<?php
// app/Http/Controllers/Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\UserPoint;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil semua enrollment aktif
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->filter(fn($e) => $e->progress >= 100)->count();
        $totalProgress = $enrollments->avg('progress') ?? 0;

        // Sertifikat
        $certificates = Certificate::where('user_id', $user->id)->count();
        $latestCertificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->take(5)
            ->get();

        // ✅ Data gamifikasi (array)
        $gamification = GamificationService::getUserProgress($user);

        // Rekomendasi kursus
        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        $recommendedCourses = Course::published()
            ->whereNotIn('id', $enrolledCourseIds)
            ->latest()
            ->take(4)
            ->get();

        // Statistik tambahan (opsional)
        $userPoints = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $totalPoints = $userPoints->total_points;
        $currentLevel = $userPoints->current_level;

        return view('student.dashboard', compact(
            'enrollments',
            'totalCourses',
            'completedCourses',
            'totalProgress',      // ← ini masih untuk progress bar kursus (integer)
            'certificates',
            'latestCertificates',
            'recommendedCourses',
            'totalPoints',
            'currentLevel',
            'gamification'        // ← kirim data gamifikasi dengan nama baru
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