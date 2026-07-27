<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Tampilkan semua ulasan untuk kursus yang diajar oleh instruktur.
     */
    public function index(Request $request)
    {
        $instructorId = Auth::id();

        $query = Review::with(['user', 'course'])
            ->whereHas('course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            });

        // Filter berdasarkan rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter berdasarkan kursus
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $reviews = $query->latest()->paginate(10);

        // Data untuk filter kursus
        $courses = Course::where('instructor_id', $instructorId)->get(['id', 'title']);

        return view('instructor.reviews.index', compact('reviews', 'courses'));
    }

    /**
     * Update balasan instruktur untuk ulasan tertentu.
     */
    public function update(Request $request, Review $review)
    {
        // Pastikan ulasan tersebut untuk kursus milik instruktur ini
        if ($review->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        // Simpan balasan (disimpan sebagai array/JSON di database)
        $review->update([
            'instructor_response' => [
                'content' => $request->response,
                'replied_at' => now()->toDateTimeString(),
                'instructor_id' => Auth::id(),
            ]
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }
}
