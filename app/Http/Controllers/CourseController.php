<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar kursus publik
     */
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'categories')
            ->where('status', 'published')
            ->where('is_active', true);

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter berdasarkan level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter berdasarkan harga (gratis / berbayar)
        if ($request->filled('price')) {
            if ($request->price == 'free') {
                $query->where('price', 0);
            } elseif ($request->price == 'paid') {
                $query->where('price', '>', 0);
            }
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest('published_at')->paginate(12)->withQueryString();

        // Ambil semua kategori aktif untuk sidebar filter
        $categories = Category::active()->withCount('courses')->get();

        // Data untuk filter (opsional)
        $levels = ['beginner', 'intermediate', 'advanced', 'all_levels'];

        return view('courses.index', compact('courses', 'categories', 'levels'));
    }

    /**
     * Menampilkan detail kursus publik
     */
    public function show($slug)
    {
        $course = Course::with([
            'instructor',
            'categories',
            'sections' => function ($q) {
                $q->orderBy('order');
            },
            'sections.lessons' => function ($q) {
                $q->orderBy('order');
            }
        ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('is_active', true)
            ->firstOrFail();

        // Cek apakah user sudah login dan sudah terdaftar di kursus ini
        $isEnrolled = false;
        $enrollment = null;
        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->first();
            $isEnrolled = !is_null($enrollment);
        }

        // Hitung rating rata-rata dari review yang sudah disetujui
        $averageRating = $course->reviews()->approved()->avg('rating') ?? 0;
        $ratingCount = $course->reviews()->approved()->count();

        // Ambil 5 review terbaru yang disetujui
        $recentReviews = $course->reviews()->approved()->latest()->limit(5)->get();

        // Kursus lain dari instruktur yang sama (rekomendasi)
        $otherCourses = Course::where('instructor_id', $course->instructor_id)
            ->where('id', '!=', $course->id)
            ->where('status', 'published')
            ->limit(3)
            ->get();

        return view('courses.show', compact(
            'course',
            'isEnrolled',
            'enrollment',
            'averageRating',
            'ratingCount',
            'recentReviews',
            'otherCourses'
        ));
    }
}