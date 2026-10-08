<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Lihat-saja — tidak ada create/edit/delete, sesuai permission 'view courses'
     * yang di-seed untuk role support.
     */
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'categories')->withCount('enrollments');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(15)->withQueryString();

        return view('support.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'categories'])->loadCount('enrollments');

        $recentEnrollments = $course->enrollments()->with('user')->latest('enrolled_at')->limit(10)->get();

        return view('support.courses.show', compact('course', 'recentEnrollments'));
    }
}
