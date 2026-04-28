<?php
// app/Http/Controllers/Admin/ArticleCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::orderBy('order')->paginate(15);
        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        ArticleCategory::create($request->all());

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berhasil dibuat.');
    }

    public function edit(ArticleCategory $articleCategory)
    {
        return view('admin.article-categories.edit', compact('articleCategory'));
    }

    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name,' . $articleCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $articleCategory->update($request->all());

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    public function destroy(ArticleCategory $articleCategory)
    {
        if ($articleCategory->articles()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki artikel.');
        }
        $articleCategory->delete();

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berhasil dihapus.');
    }
}