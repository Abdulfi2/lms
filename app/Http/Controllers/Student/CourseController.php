<?php
// app/Http/Controllers/Student/CourseController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->with(['sections.lessons', 'instructor'])
            ->firstOrFail();

        // Check if user is enrolled
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            // If not enrolled, redirect to course detail page (public)
            return redirect()->route('courses.show', $course->slug);
        }

        $sections = $course->sections()->with([
            'lessons' => function ($q) {
                $q->orderBy('order');
            }
        ])->orderBy('order')->get();

        // Get first incomplete lesson as current
        $currentLesson = null;
        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                $completed = LessonCompletion::where('user_id', Auth::id())
                    ->where('lesson_id', $lesson->id)
                    ->exists();
                if (!$completed) {
                    $currentLesson = $lesson;
                    break 2;
                }
            }
        }

        return view('student.courses.show', compact('course', 'enrollment', 'sections', 'currentLesson'));
    }
}