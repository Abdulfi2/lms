<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'enrolled_courses' => auth()->user()->enrolledCourses()->count(),
            'completed_courses' => auth()->user()->enrolledCourses()->wherePivot('status', 'completed')->count(),
            'certificates' => auth()->user()->certificates()->count(),
            'total_points' => auth()->user()->profile?->statistics['total_points'] ?? 0,
            'recent_courses' => auth()->user()->enrolledCourses()->latest()->take(5)->get(),
        ];

        return view('instructor.dashboard', $data);
    }
}