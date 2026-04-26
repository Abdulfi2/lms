<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Jobs\GenerateCertificateJob;
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
            ->firstOrFail();

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
            // ==================== VALIDATION ====================

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
                'completed_at' => $isNowCompleted && !$wasCompleted ? now() : $enrollment->completed_at,
            ]);

            DB::commit();

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

            $message = 'Lesson selesai!';
            if ($isNowCompleted && !$wasCompleted) {
                $message = $certificateGenerated
                    ? 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat telah dibuat!'
                    : 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat sedang diproses.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'progress' => $progress,
                'completed' => $isNowCompleted,
                'certificate_generated' => $certificateGenerated,
                'next_lesson_url' => $this->getNextLessonUrl($course, $lesson)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

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

    /**
     * Get next lesson URL for auto-redirect.
     */
    private function getNextLessonUrl($course, $currentLesson)
    {
        $nextLesson = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->where(function ($q) use ($currentLesson) {
                $q->where('sections.order', '>', $currentLesson->section->order)
                    ->orWhere(function ($sq) use ($currentLesson) {
                        $sq->where('sections.order', $currentLesson->section->order)
                            ->where('lessons.order', '>', $currentLesson->order);
                    });
            })
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.*')
            ->first();

        if ($nextLesson) {
            return route('student.lessons.show', [$course, $nextLesson]);
        }

        return route('student.courses.show', $course->slug);
    }
}