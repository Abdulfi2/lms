<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;

class WishlistController extends Controller
{
    /**
     * Statistik kursus yang paling banyak di-wishlist siswa — membantu admin/instruktur
     * melihat minat pasar terhadap kursus yang belum tentu langsung dibeli.
     */
    public function index()
    {
        $courses = Course::withCount('wishlists')
            ->having('wishlists_count', '>', 0)
            ->orderByDesc('wishlists_count')
            ->paginate(20);

        $totalWishlisted = Course::has('wishlists')->count();

        return view('admin.wishlists.index', compact('courses', 'totalWishlisted'));
    }
}
