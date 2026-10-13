<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $popularCourses = Course::where('status', 'published')
            ->orderBy('total_students', 'desc')
            ->limit(6)
            ->get();

        $testimonials = Testimonial::active()->ordered()->limit(6)->get();

        return view('client.pages.home', compact('popularCourses', 'testimonials'));
    }
}