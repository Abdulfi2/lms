<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'categories')
            ->where('status', 'published');
        // ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%")
                ->orWhere('short_description', 'like', "%{$request->search}%");
        }

        $courses = $query->latest('published_at')->paginate(12);
        $categories = Category::active()->withCount('courses')->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show($slug)
    {
        $course = Course::with(['instructor', 'categories', 'sections.lessons'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Check if user is enrolled (if logged in)
        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = auth()->user()->enrollments()
                ->where('course_id', $course->id)
                ->exists();
        }

        return view('courses.show', compact('course', 'isEnrolled'));
    }
}