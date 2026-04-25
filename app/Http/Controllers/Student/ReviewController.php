<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function create(Course $course)
    {
        // Cek apakah student terdaftar di course ini
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda harus terdaftar di kursus ini untuk memberikan review.');
        }

        // Cek progress minimal 50% (bisa disesuaikan)
        if ($enrollment->progress < 50) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Anda harus menyelesaikan minimal 50% kursus untuk memberikan review.');
        }

        // Cek apakah sudah pernah review
        $existing = Review::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('info', 'Anda sudah memberikan review untuk kursus ini.');
        }

        return view('student.reviews.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Validasi ulang (sama seperti di create)
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || $enrollment->progress < 50) {
            return back()->with('error', 'Tidak memenuhi syarat untuk memberikan review.');
        }

        $exists = Review::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah memberikan review.');
        }

        try {
            DB::beginTransaction();

            $review = Review::create([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => false, // butuh approval admin
            ]);

            DB::commit();

            return redirect()->route('student.courses.show', $course->slug)
                ->with('success', 'Terima kasih! Review Anda akan ditampilkan setelah disetujui admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan review: ' . $e->getMessage());
        }
    }
}