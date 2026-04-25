<?php
// app/Http/Controllers/Student/LessonController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // Pastikan lesson benar-benar berada dalam course ini
        if ($lesson->section->course_id != $course->id) {
            abort(404);
        }

        // Cek enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Ambil semua lesson dalam course dengan urutan yang benar (berdasarkan section.order, lesson.order)
        $allLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->whereNull('lessons.deleted_at')
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.*')
            ->get();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1) ? $allLessons[$currentIndex + 1] : null;

        // Cek apakah lesson sudah selesai
        $isCompleted = LessonCompletion::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->exists();

        return view('student.lessons.show', compact('course', 'lesson', 'prevLesson', 'nextLesson', 'isCompleted'));
    }

    public function complete(Request $request, Lesson $lesson)
    {
        try {
            // Cek lesson dan section
            if (!$lesson->section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lesson tidak valid (section tidak ditemukan)'
                ], 400);
            }

            $course = $lesson->section->course;
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus tidak ditemukan untuk lesson ini'
                ], 400);
            }

            $user = auth()->user();
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar di kursus ini'
                ], 403);
            }

            // Cek sudah selesai sebelumnya
            $exists = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson sudah selesai sebelumnya'
                ]);
            }

            // Simpan completion
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $request->input('time_spent', 0),
            ]);

            // Update progress enrollment
            $totalLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                ->where('sections.course_id', $course->id)
                ->count();

            $completedLessons = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                    ->where('sections.course_id', $course->id)
                    ->select('lessons.id'))
                ->count();

            $progress = ($completedLessons / max($totalLessons, 1)) * 100;
            $enrollment->update([
                'progress' => $progress,
                'completed_at' => $progress >= 100 ? now() : null,
            ]);

            // Jika progress 100%, panggil event untuk generate certificate
            if ($progress >= 100 && !$enrollment->completed_at) {
                // event(new CourseCompleted($user, $course)); // comment dulu jika belum ada event
            }

            return response()->json([
                'success' => true,
                'message' => 'Lesson selesai!',
                'progress' => round($progress)
            ]);

        } catch (\Exception $e) {
            \Log::error('Lesson completion error: ' . $e->getMessage(), [
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
}