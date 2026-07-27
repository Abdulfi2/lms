<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;

class InstructorProfileController extends Controller
{
    /**
     * Halaman profil publik instruktur — dapat dilihat siapa saja sebelum enroll.
     */
    public function show(User $instructor)
    {
        abort_unless($instructor->hasRole('instructor'), 404);
        abort_unless(optional($instructor->profile)->is_public, 404);

        $courses = Course::where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->orderByDesc('total_students')
            ->get();

        $totalStudents = $courses->sum('total_students');
        $avgRating = $courses->count() > 0 ? round($courses->avg('average_rating'), 1) : 0;

        return view('public.instructor-profile', compact('instructor', 'courses', 'totalStudents', 'avgRating'));
    }
}
