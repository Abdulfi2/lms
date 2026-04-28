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
    /**
     * Menampilkan form review.
     */
    public function create($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $user = auth()->user();

        // Debug: cek apakah user terdaftar
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda harus terdaftar di kursus ini terlebih dahulu.');
        }

        // Cek progress (bisa 100% atau minimal 50%)
        if ($enrollment->progress < 50) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Anda harus menyelesaikan minimal 50% kursus untuk memberikan review. Progress Anda: ' . round($enrollment->progress) . '%');
        }

        // Cek apakah sudah pernah review
        $existingReview = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('info', 'Anda sudah memberikan review untuk kursus ini.');
        }

        return view('student.reviews.create', compact('course', 'enrollment'));
    }

    /**
     * Menyimpan review.
     */
    public function store(Request $request, Course $course)
    {
        $user = auth()->user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Validasi ulang
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || $enrollment->progress < 50) {
            return back()->with('error', 'Anda belum memenuhi syarat untuk memberikan review.');
        }

        $exists = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah memberikan review untuk kursus ini.');
        }

        try {
            DB::beginTransaction();

            $review = Review::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => false,
            ]);

            $enrollment->update(['review_given' => true]);

            DB::commit();

            return redirect()->route('student.courses.show', $course->slug)
                ->with('success', 'Terima kasih! Review Anda akan ditampilkan setelah disetujui admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan review: ' . $e->getMessage());
        }
    }

    public function edit($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $user = auth()->user();

        $review = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if ($review->is_approved) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Review sudah disetujui dan tidak dapat diedit.');
        }

        return view('student.reviews.edit', compact('course', 'review'));
    }

    public function update(Request $request, Course $course)
    {
        $user = auth()->user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if ($review->is_approved) {
            return back()->with('error', 'Review sudah disetujui dan tidak dapat diedit.');
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('student.courses.show', $course->slug)
            ->with('success', 'Review berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $user = auth()->user();

        $review = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if ($review->is_approved) {
            return back()->with('error', 'Review yang sudah disetujui tidak dapat dihapus.');
        }

        $review->delete();

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update(['review_given' => false]);

        return redirect()->route('student.courses.show', $course->slug)
            ->with('success', 'Review berhasil dihapus.');
    }
}