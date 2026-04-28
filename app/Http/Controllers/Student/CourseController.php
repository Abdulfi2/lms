<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Certificate;
use App\Models\Review;
use App\Models\UserPoint;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda harus terdaftar di kursus ini terlebih dahulu.');
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

        // 6. Jika semua lesson sudah selesai, tandai enrollment completed
        $isCourseCompleted = $completedLessons >= $totalLessons && $totalLessons > 0;

        if ($isCourseCompleted && $enrollment->status != 'completed') {
            DB::beginTransaction();
            try {
                $enrollment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'progress' => 100
                ]);

                // Generate sertifikat otomatis
                if (!$enrollment->certificate_issued_at) {
                    $enrollment->certificate_issued_at = now();
                    $enrollment->save();
                    \App\Jobs\GenerateCertificateJob::dispatch($enrollment);
                }

                // Gamification: tambah poin
                GamificationService::courseCompleted(Auth::user(), $course);
                GamificationService::addPoints(Auth::user(), 100, "Menyelesaikan kursus: {$course->title}");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Course completion error: ' . $e->getMessage());
            }
        }

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