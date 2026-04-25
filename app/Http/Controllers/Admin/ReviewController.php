<?php
// app/Http/Controllers/Admin/ReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'course']);

        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status == 'approved') {
                $query->where('is_approved', true);
            }
        }

        $reviews = $query->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update rating stats di course
        $review->course->updateRatingStats();

        return response()->json([
            'success' => true,
            'message' => 'Review disetujui dan rating kursus diperbarui.'
        ]);
    }

    public function destroy(Review $review)
    {
        $course = $review->course;
        $review->delete();
        $course->updateRatingStats();

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil dihapus.'
        ]);
    }
}