<?php
// app/Http/Controllers/Admin/ArticleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Upload gambar yang disisipkan di dalam konten artikel (dipanggil dari
     * dalam text editor, bukan form submission biasa).
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $path = $request->file('image')->store('articles/content', 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }

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
        $tags = Tag::active()->orderBy('name')->get();
        return view('admin.articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:articles,slug',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
        ]);

        $data = Arr::except($validated, ['featured_image', 'tags', 'og_image', 'slug']);
        $data['user_id'] = auth()->id();
        $data['is_featured'] = $request->boolean('is_featured');
        // Slug kosong dibiarkan null supaya Article::boot() yang auto-generate dari judul
        // (lihat creating() hook) — jangan simpan string kosong di sini.
        $data['slug'] = $validated['slug'] ?? null;
        // Input datetime yang dikosongkan mengirim '' (bukan absen), dan '' bukan nilai
        // DATETIME yang valid di MySQL — normalisasi ke null di sini.
        $data['published_at'] = $data['published_at'] ?: null;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $data['featured_image'] = $path;
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('articles', 'public');
        }

        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        $article = Article::create($data);

        if ($request->filled('tags')) {
            $article->tags()->sync($request->tags);
            Tag::whereIn('id', $request->tags)->increment('usage_count');
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        $tags = Tag::active()->orderBy('name')->get();
        $article->load('tags');
        $selectedTags = $article->tags->pluck('id')->toArray();
        return view('admin.articles.edit', compact('article', 'categories', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', \Illuminate\Validation\Rule::unique('articles', 'slug')->ignore($article->id)],
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
        ]);

        $data = Arr::except($validated, ['featured_image', 'tags', 'og_image', 'slug']);
        $data['is_featured'] = $request->boolean('is_featured');
        // Jangan timpa slug yang sudah ada dengan kosong — biarkan slug lama tetap
        // kalau field ini dikosongkan saat edit (beda dari create, di sini tidak ada
        // auto-generate ulang supaya URL artikel yang sudah dibagikan tidak berubah).
        if (!empty($validated['slug'])) {
            $data['slug'] = $validated['slug'];
        }
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

        if ($request->hasFile('og_image')) {
            if ($article->og_image && Storage::disk('public')->exists($article->og_image)) {
                Storage::disk('public')->delete($article->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('articles', 'public');
        }

        if ($request->status === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);

        $oldTags = $article->tags->pluck('id')->toArray();
        $newTags = $request->input('tags', []);

        Tag::whereIn('id', array_diff($oldTags, $newTags))->decrement('usage_count');
        Tag::whereIn('id', array_diff($newTags, $oldTags))->increment('usage_count');

        $article->tags()->sync($newTags);

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