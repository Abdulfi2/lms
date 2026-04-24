<?php
// app/Http/Controllers/Student/LessonController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // Check enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Get all lessons in order for navigation
        $allLessons = $course->lessons()->orderBy('order')->get();
        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $allLessons[$currentIndex - 1] ?? null;
        $nextLesson = $allLessons[$currentIndex + 1] ?? null;

        // Check if already completed
        $isCompleted = LessonCompletion::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->exists();

        return view('student.lessons.show', compact('course', 'lesson', 'prevLesson', 'nextLesson', 'isCompleted'));
    }

    public function complete(Request $request, Lesson $lesson)
    {
        try {
            DB::beginTransaction();

            // Check if already completed
            $existing = LessonCompletion::where('user_id', Auth::id())
                ->where('lesson_id', $lesson->id)
                ->first();

            if (!$existing) {
                LessonCompletion::create([
                    'user_id' => Auth::id(),
                    'lesson_id' => $lesson->id,
                    'is_completed' => true,
                    'completed_at' => now(),
                ]);

                // Update progress for the course
                $course = $lesson->section->course;
                $totalLessons = $course->lessons()->count();
                $completedLessons = LessonCompletion::where('user_id', Auth::id())
                    ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                    ->count();

                $progressPercent = ($completedLessons / max($totalLessons, 1)) * 100;

                // Update enrollment progress
                $enrollment = Enrollment::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->first();

                if ($enrollment) {
                    $enrollment->progress = $progressPercent;
                    if ($progressPercent >= 100) {
                        $enrollment->status = 'completed';
                        $enrollment->completed_at = now();
                    }
                    $enrollment->save();
                }

                // Update progress table
                Progress::updateOrCreate(
                    ['user_id' => Auth::id(), 'course_id' => $course->id],
                    [
                        'completed_lessons' => $completedLessons,
                        'percentage' => $progressPercent,
                        'last_activity_at' => now(),
                        'is_completed' => $progressPercent >= 100,
                        'completed_at' => $progressPercent >= 100 ? now() : null,
                    ]
                );
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Lesson selesai!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}