<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;

class CategoryController extends Controller
{
    /**
     * Daftar kategori artikel (read-only) — editor cuma bisa menambah kategori
     * baru lewat form tulis artikel, tidak punya akses edit/hapus seperti admin.
     */
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('editor.categories.index', compact('categories'));
    }
}
