<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Jobs\GenerateCertificateJob;
use App\Models\UserPoint;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    /**
     * Display lesson content.
     */
    public function show(Course $course, Lesson $lesson)
    {
        // Ensure lesson belongs to course
        if ($lesson->section->course_id != $course->id) {
            abort(404);
        }

        // Check enrollment
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        if ($enrollment->payment_status !== 'paid') {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Selesaikan pembayaran terlebih dahulu untuk mengakses materi ini.');
        }

        // Get all lessons for navigation
        $allLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.*')
            ->get();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1)
            ? $allLessons[$currentIndex + 1]
            : null;

        // Check completion status
        $isCompleted = LessonCompletion::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->exists();

        // Get lesson resources
        $resources = $lesson->resources()->orderBy('order')->get();

        // Get user progress
        $userPoint = UserPoint::firstOrCreate(['user_id' => auth()->id()]);

        return view('student.lessons.show', compact(
            'course',
            'lesson',
            'prevLesson',
            'nextLesson',
            'isCompleted',
            'resources',
            'enrollment',
            'userPoint'
        ));
    }

    /**
     * Mark lesson as completed.
     */
    public function complete(Request $request, Lesson $lesson)
    {
        try {
            // Check lesson and section relationship
            if (!$lesson->section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lesson tidak valid (section tidak ditemukan)'
                ], 400);
            }

            // Get course from lesson
            $course = $lesson->section->course;
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus tidak ditemukan untuk lesson ini'
                ], 400);
            }

            // Check if user is authenticated
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Check enrollment
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar di kursus ini'
                ], 403);
            }

            if (!in_array($enrollment->status, ['active', 'completed']) || ($enrollment->expires_at && $enrollment->expires_at->isPast())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Anda ke kursus ini sudah tidak aktif'
                ], 403);
            }

            if ($enrollment->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selesaikan pembayaran terlebih dahulu untuk mengakses kursus ini'
                ], 402);
            }

            // ==================== PREVENT DUPLICATE ====================

            $alreadyCompleted = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->exists();

            if ($alreadyCompleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson sudah selesai sebelumnya',
                    'progress' => round($enrollment->progress)
                ]);
            }

            // ==================== SAVE COMPLETION ====================

            DB::beginTransaction();

            // Create lesson completion record
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $request->input('time_spent', 0),
                'watch_percentage' => $request->input('watch_percentage', 100),
                'last_position' => $request->input('last_position', 0),
            ]);

            // Calculate total lessons in course
            $totalLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                ->where('sections.course_id', $course->id)
                ->count();

            // Calculate completed lessons
            $completedLessons = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                    ->where('sections.course_id', $course->id)
                    ->select('lessons.id'))
                ->count();

            // Calculate progress percentage
            $progress = ($completedLessons / max($totalLessons, 1)) * 100;
            $progress = round($progress, 2);

            // Update enrollment progress
            $wasCompleted = $enrollment->progress >= 100;
            $isNowCompleted = $progress >= 100;

            $enrollment->update([
                'progress' => $progress,
                'status' => $isNowCompleted ? 'completed' : $enrollment->status,
                'completed_at' => $isNowCompleted && !$wasCompleted ? now() : $enrollment->completed_at,
            ]);

            DB::commit();

            // ==================== GAMIFICATION - UPDATE POINTS EVERY LESSON ====================
            // Update daily streak
            GamificationService::updateStreak($user);

            // Add points for completing this lesson
            $lessonPoints = $lesson->points ?? 10;
            GamificationService::addPoints($user, $lessonPoints, "Menyelesaikan lesson: {$lesson->title} (+{$lessonPoints} poin)");

            // Check badges and achievements after earning points
            GamificationService::checkBadges($user);
            GamificationService::checkAchievements($user);

            // ==================== GENERATE CERTIFICATE ====================

            $certificateGenerated = false;
            if ($isNowCompleted && !$wasCompleted && !$enrollment->certificate_issued_at) {
                try {
                    // Update certificate issued timestamp
                    $enrollment->certificate_issued_at = now();
                    $enrollment->saveQuietly();

                    // Dispatch job to generate certificate (sync for immediate result)
                    GenerateCertificateJob::dispatchSync($enrollment);
                    $certificateGenerated = true;

                    // Add bonus points for completing course
                    GamificationService::addPoints($user, 100, "Menyelesaikan kursus: {$course->title} (+100 bonus poin)");
                    GamificationService::courseCompleted($user, $course);

                    Log::info('Certificate generation dispatched', [
                        'enrollment_id' => $enrollment->id,
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'progress' => $progress
                    ]);
                } catch (\Exception $e) {
                    Log::error('Certificate generation failed: ' . $e->getMessage(), [
                        'enrollment_id' => $enrollment->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // ==================== RESPONSE ====================

            $message = 'Lesson selesai! +' . $lessonPoints . ' poin';
            if ($isNowCompleted && !$wasCompleted) {
                $message = $certificateGenerated
                    ? 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat telah dibuat! +100 bonus poin'
                    : 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat sedang diproses. +100 bonus poin';
            }

            // Get next lesson URL
            $nextLessonUrl = null;
            $nextLesson = $this->getNextLesson($course, $lesson);
            if ($nextLesson) {
                $nextLessonUrl = route('student.lessons.show', [$course, $nextLesson]);
            }

            // Get updated user points
            $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'progress' => $progress,
                'completed' => $isNowCompleted,
                'certificate_generated' => $certificateGenerated,
                'points_earned' => $lessonPoints,
                'total_points' => $userPoint->total_points,
                'current_level' => $userPoint->current_level,
                'next_lesson_url' => $nextLessonUrl
            ]);

        } catch (\Exception $e) {
            // DB::beginTransaction() hanya terjadi di pertengahan method ini, dan sudah di-commit
            // sebelum blok gamifikasi/sertifikat berjalan — exception dari kode sebelum begin atau
            // setelah commit tidak punya transaksi aktif untuk di-rollback ("There is no active
            // transaction"), yang tadinya menutupi pesan error asli di atas.
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Lesson completion error: ' . $e->getMessage(), [
                'lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan progress: ' . $e->getMessage()
            ], 500);
        }
    }

    public function trackTime(Request $request, Lesson $lesson)
    {
        try {
            // Validasi apakah user terdaftar di course
            $course = $lesson->section->course;
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->where('payment_status', 'paid')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists();

            if (!$enrollment) {
                return response()->json(['success' => false], 403);
            }

            // Simpan atau update waktu belajar
            $completion = LessonCompletion::firstOrCreate(
                ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
                ['time_spent' => 0]
            );

            $completion->increment('time_spent', $request->input('time_spent', 0));

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get next lesson URL for auto-redirect.
     */
    /**
     * Get the next unfinished lesson in course.
     */
    private function getNextLesson($course, $currentLesson)
    {
        // Ambil semua lesson dalam course dengan urutan yang benar
        $allLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order', 'asc')
            ->orderBy('lessons.order', 'asc')
            ->select('lessons.*', 'sections.order as section_order')
            ->get();

        // Cari lesson yang belum selesai setelah current lesson
        $foundCurrent = false;
        foreach ($allLessons as $lessonItem) {
            if ($foundCurrent) {
                // Cek apakah lesson ini sudah selesai?
                $isCompleted = LessonCompletion::where('user_id', auth()->id())
                    ->where('lesson_id', $lessonItem->id)
                    ->exists();

                if (!$isCompleted) {
                    return $lessonItem;
                }
            }

            if ($lessonItem->id == $currentLesson->id) {
                $foundCurrent = true;
            }
        }

        return null;
    }

}