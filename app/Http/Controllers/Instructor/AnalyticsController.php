<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();
        $courses = Course::where('instructor_id', $instructorId)->get();
        $courseIds = $courses->pluck('id');

        // 1. Enrollment Overview
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $enrollmentsThisMonth = Enrollment::whereIn('course_id', $courseIds)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();
        
        // 2. Performance Stats
        $avgCourseRating = $courses->avg('average_rating') ?? 0;
        $totalCompletedLessons = DB::table('lesson_completions')
            ->whereIn('lesson_id', function($query) use ($courseIds) {
                $query->select('lessons.id')
                    ->from('lessons')
                    ->join('sections', 'lessons.section_id', '=', 'sections.id')
                    ->whereIn('sections.course_id', $courseIds);
            })->count();

        // 3. Enrollment Trend (Last 30 Days)
        $enrollmentTrend = Enrollment::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereIn('course_id', $courseIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Course Performance Breakdown
        $coursePerformance = Course::where('instructor_id', $instructorId)
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->orderBy('enrollments_count', 'desc')
            ->get();

        // 5. Quiz & Assignment Stats
        $totalSubmissions = Submission::whereHas('assignment', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->count();

        $avgQuizScore = QuizAttempt::whereHas('quiz', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->avg('score') ?? 0;

        return view('instructor.analytics', compact(
            'totalEnrollments',
            'enrollmentsThisMonth',
            'avgCourseRating',
            'totalCompletedLessons',
            'enrollmentTrend',
            'coursePerformance',
            'totalSubmissions',
            'avgQuizScore'
        ));
    }
}
