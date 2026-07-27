<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Certificate;
use App\Models\Review;
use App\Models\UserPoint;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman BELAJAR (sections/lessons/progress/sertifikat), wajib enrollment aktif & lunas.
 * Untuk halaman kursus publik/marketing (browse tanpa enrollment), lihat
 * App\Http\Controllers\CourseController — namanya sama, fungsinya beda.
 */
class CourseController extends Controller
{
    public function show($slug)
    {
        // 1. Ambil data kursus dengan eager loading
        $course = Course::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'instructor',
                'sections' => fn($q) => $q->orderBy('order'),
                'sections.lessons' => fn($q) => $q->orderBy('order'),
                'categories',
                'tags'
            ])
            ->firstOrFail();

        // 2. Cek enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda harus terdaftar di kursus ini terlebih dahulu, atau akses Anda sudah kedaluwarsa.');
        }

        if ($enrollment->payment_status !== 'paid') {
            // Tampilkan halaman status yang jelas, bukan melempar balik ke halaman
            // marketing dengan pesan sekali tayang yang hilang setelah reload.
            return view('student.courses.payment-pending', compact('course', 'enrollment'));
        }

        // 3. Ambil sections dan lessons
        $sections = $course->sections;

        // 4. Hitung progress per section
        $totalLessons = 0;
        $completedLessons = 0;
        $sectionProgress = [];

        foreach ($sections as $section) {
            $sectionTotal = $section->lessons->count();
            $sectionCompleted = 0;

            foreach ($section->lessons as $lesson) {
                $totalLessons++;
                $isCompleted = LessonCompletion::where('user_id', Auth::id())
                    ->where('lesson_id', $lesson->id)
                    ->exists();
                if ($isCompleted) {
                    $completedLessons++;
                    $sectionCompleted++;
                }
            }

            $sectionProgress[$section->id] = [
                'total' => $sectionTotal,
                'completed' => $sectionCompleted,
                'percentage' => $sectionTotal > 0 ? round(($sectionCompleted / $sectionTotal) * 100) : 0
            ];
        }

        // 5. Cari lesson pertama yang belum selesai
        $currentLesson = null;
        $nextLesson = null;
        $prevLesson = null;

        $allLessons = [];
        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                $allLessons[] = $lesson;
            }
        }

        foreach ($allLessons as $index => $lesson) {
            $isCompleted = LessonCompletion::where('user_id', Auth::id())
                ->where('lesson_id', $lesson->id)
                ->exists();
            if (!$isCompleted && !$currentLesson) {
                $currentLesson = $lesson;
                $prevLesson = $index > 0 ? $allLessons[$index - 1] : null;
            }
            if ($currentLesson && $lesson->id == $currentLesson->id) {
                $nextLesson = $index + 1 < count($allLessons) ? $allLessons[$index + 1] : null;
            }
        }

        // 6. Status penyelesaian kursus (untuk tampilan saja — status/sertifikat/poin
        // sudah ditandai secara otomatis di LessonController::complete() saat lesson
        // terakhir diselesaikan, bukan di sini, karena GET request tidak boleh punya efek samping).
        $isCourseCompleted = $enrollment->status === 'completed'
            || ($completedLessons >= $totalLessons && $totalLessons > 0);

        // 7. Ambil sertifikat jika ada
        $certificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        // 8. Ambil review user
        $userReview = Review::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        // 9. Hitung review stats
        $totalReviews = Review::where('course_id', $course->id)
            ->where('is_approved', true)
            ->count();
        $averageRating = Review::where('course_id', $course->id)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        // 10. Ambil 5 review terbaru
        $recentReviews = Review::with('user')
            ->where('course_id', $course->id)
            ->where('is_approved', true)
            ->latest()
            ->limit(5)
            ->get();

        // 11. Ambil user points
        $userPoints = UserPoint::firstOrCreate(['user_id' => Auth::id()]);

        // 12. Rekomendasi kursus lain
        $recommendedCourses = Course::where('status', 'published')
            ->where('id', '!=', $course->id)
            ->whereDoesntHave('enrollments', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->limit(3)
            ->get();

        return view('student.courses.show', compact(
            'course',
            'enrollment',
            'sections',
            'currentLesson',
            'prevLesson',
            'nextLesson',
            'isCourseCompleted',
            'certificate',
            'userReview',
            'totalReviews',
            'averageRating',
            'recentReviews',
            'userPoints',
            'recommendedCourses',
            'sectionProgress',
            'totalLessons',
            'completedLessons'
        ));
    }
}