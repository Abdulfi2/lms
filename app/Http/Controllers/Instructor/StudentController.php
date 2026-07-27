<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Daftar semua siswa yang terdaftar di kursus instructor.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        // Ambil semua course milik instructor
        $courseIds = Course::where('instructor_id', $instructorId)->pluck('id');

        // Ambil semua enrollment beserta user dan course
        $query = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->where('status', 'active');

        // Filter berdasarkan nama siswa
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Filter berdasarkan kursus
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter dropdown kursus
        $courses = Course::where('instructor_id', $instructorId)->get(['id', 'title']);

        return view('instructor.students.index', compact('enrollments', 'courses'));
    }

    /**
     * Menampilkan progress detail siswa pada suatu kursus.
     */
    public function studentCourseProgress(User $user, Course $course)
    {
        // Pastikan course milik instructor ini
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Ambil semua lesson dalam course
        $lessons = $course->lessons()->orderBy('order')->get();

        // Ambil completion status untuk setiap lesson
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        // Hitung progress
        $totalLessons = $lessons->count();
        $completedCount = count($completedLessonIds);
        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return view('instructor.students.progress', compact('user', 'course', 'enrollment', 'lessons', 'completedLessonIds', 'progress', 'completedCount', 'totalLessons'));
    }
}