<?php

namespace App\Http\Controllers;

use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $popularCourses = Course::where('status', 'published')
            ->orderBy('total_students', 'desc')
            ->limit(6)
            ->get();

        return view('client.pages.home', compact('popularCourses'));
    }
}