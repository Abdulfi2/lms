<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
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
        $tags = Tag::active()->orderBy('name')->get();

        return view('author.articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:articles,slug',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            // Author tidak boleh langsung publish — cuma editor/admin yang bisa (lihat ArticlePolicy::publish).
            'status' => 'required|in:draft,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'og_image', 'slug'])->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = $validated['slug'] ?? null;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('articles', 'public');
        }

        $article = Article::create($data);

        if ($request->filled('tags')) {
            $article->tags()->sync($request->tags);
            Tag::whereIn('id', $request->tags)->increment('usage_count');
        }

        return redirect()->route('author.articles.index')
            ->with('success', 'Artikel berhasil disimpan sebagai draft. Admin/editor akan meninjau sebelum dipublikasikan.');
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        $tags = Tag::active()->orderBy('name')->get();
        $article->load('tags');
        $selectedTags = $article->tags->pluck('id')->toArray();

        return view('author.articles.edit', compact('article', 'categories', 'tags', 'selectedTags'));
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
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', \Illuminate\Validation\Rule::unique('articles', 'slug')->ignore($article->id)],
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => $statusRule,
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'status', 'og_image', 'slug'])->all();

        if (!empty($validated['slug'])) {
            $data['slug'] = $validated['slug'];
        }

        if ($article->status !== 'published') {
            $data['status'] = $validated['status'];
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        if ($request->hasFile('og_image')) {
            if ($article->og_image && Storage::disk('public')->exists($article->og_image)) {
                Storage::disk('public')->delete($article->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('articles', 'public');
        }

        $article->update($data);

        $oldTags = $article->tags->pluck('id')->toArray();
        $newTags = $request->input('tags', []);

        Tag::whereIn('id', array_diff($oldTags, $newTags))->decrement('usage_count');
        Tag::whereIn('id', array_diff($newTags, $oldTags))->increment('usage_count');

        $article->tags()->sync($newTags);

        return redirect()->route('author.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }
}
