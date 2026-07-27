<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('course.instructor')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('student.wishlist.index', compact('wishlists'));
    }

    /**
     * Tambah/hapus kursus dari wishlist (toggle).
     */
    public function toggle(Request $request, Course $course)
    {
        $existing = Wishlist::where('user_id', auth()->id())->where('course_id', $course->id)->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message = 'Kursus dihapus dari wishlist.';
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'course_id' => $course->id]);
            $wishlisted = true;
            $message = 'Kursus ditambahkan ke wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Kursus dihapus dari wishlist.');
    }
}
