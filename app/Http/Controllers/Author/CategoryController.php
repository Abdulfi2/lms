<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;

class CategoryController extends Controller
{
    /**
     * Daftar kategori artikel (read-only) — author cuma bisa menambah kategori
     * baru lewat form tulis artikel (lihat ArticleController::storeCategory),
     * tidak punya akses edit/hapus seperti admin.
     */
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('author.categories.index', compact('categories'));
    }
}
