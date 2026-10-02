<?php
// app/Http/Controllers/Admin/ArticleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'category']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
        ]);

        $data = Arr::except($validated, 'featured_image');
        $data['user_id'] = auth()->id();
        // Input datetime yang dikosongkan mengirim '' (bukan absen), dan '' bukan nilai
        // DATETIME yang valid di MySQL — normalisasi ke null di sini.
        $data['published_at'] = $data['published_at'] ?: null;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $data['featured_image'] = $path;
        }

        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
        ]);

        $data = Arr::except($validated, 'featured_image');
        // Input datetime yang dikosongkan mengirim '' (bukan absen), dan '' bukan nilai
        // DATETIME yang valid di MySQL — normalisasi ke null di sini.
        $data['published_at'] = $data['published_at'] ?: null;

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $data['featured_image'] = $path;
        }

        if ($request->status === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
            Storage::disk('public')->delete($article->featured_image);
        }
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function toggleStatus(Article $article)
    {
        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? now() : null,
        ]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }
}