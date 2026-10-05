<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);

        $query = Article::where('user_id', Auth::id())->with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('author.articles.index', compact('articles'));
    }

    public function create()
    {
        $this->authorize('create', Article::class);

        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();

        return view('author.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            // Author tidak boleh langsung publish — cuma editor/admin yang bisa (lihat ArticlePolicy::publish).
            'status' => 'required|in:draft,archived',
        ]);

        $data = collect($validated)->except('featured_image')->all();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->route('author.articles.index')
            ->with('success', 'Artikel berhasil disimpan sebagai draft. Admin/editor akan meninjau sebelum dipublikasikan.');
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();

        return view('author.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        // Begitu sudah published, status jadi wewenang editor/admin (lihat
        // ArticlePolicy::publish) — author masih boleh edit isi artikelnya,
        // tapi tidak boleh ikut mengubah statusnya lewat form ini.
        $statusRule = $article->status === 'published' ? 'nullable' : 'required|in:draft,archived';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => $statusRule,
        ]);

        $data = collect($validated)->except(['featured_image', 'status'])->all();

        if ($article->status !== 'published') {
            $data['status'] = $validated['status'];
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('author.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }
}
