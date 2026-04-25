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
            ->where('status', 'published') // tambahkan
            ->with([
                'instructor',
                'sections' => fn($q) => $q->orderBy('order'),
                'sections.lessons' => fn($q) => $q->orderBy('order')
            ])
            ->firstOrFail();

        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed']) // tambahkan
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->slug);
        }

        $sections = $course->sections;

        // Cari lesson pertama yang belum selesai
        $currentLesson = null;
        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                if (!LessonCompletion::where('user_id', Auth::id())->where('lesson_id', $lesson->id)->exists()) {
                    $currentLesson = $lesson;
                    break 2;
                }
            }
        }

        // Jika semua lesson sudah selesai, arahkan ke halaman sertifikat (atah tampilkan pesan di view)
        if (!$currentLesson && $enrollment->status != 'completed') {
            // Tandai enrollment selesai
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
            // Trigger event untuk generate certificate (jika sudah ada)
            // event(new CourseCompleted(Auth::user(), $course));
        }

        return view('student.courses.show', compact('course', 'enrollment', 'sections', 'currentLesson'));
    }
}