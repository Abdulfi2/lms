<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
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

        return view('student.dashboard', $data);
    }
}