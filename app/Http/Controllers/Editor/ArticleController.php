<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Upload gambar yang disisipkan di dalam konten artikel (dipanggil dari
     * dalam text editor, bukan form submission biasa).
     */
    public function uploadImage(Request $request)
    {
        $this->authorize('create', Article::class);

        $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $path = $request->file('image')->store('articles/content', 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }

    /**
     * Daftar artikel dengan 3 tab: menunggu review (draft milik author lain),
     * artikel saya, dan semua artikel.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);

        $tab = $request->get('tab', 'pending');
        $query = Article::with(['author', 'category'])->withCount('comments');

        if ($tab === 'pending') {
            $query->where('status', 'draft')->where('user_id', '!=', Auth::id());
        } elseif ($tab === 'mine') {
            $query->where('user_id', Auth::id());
        }
        // $tab === 'all' -> tidak ada filter kepemilikan/status tambahan.

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('author', fn ($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status') && $tab !== 'pending') {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $pendingCount = Article::where('status', 'draft')->where('user_id', '!=', Auth::id())->count();

        return view('editor.articles.index', compact('articles', 'tab', 'pendingCount'));
    }

    public function show(Article $article)
    {
        $this->authorize('view', $article);

        $article->load(['author', 'category', 'tags']);

        return view('editor.articles.show', compact('article'));
    }

    public function create()
    {
        $this->authorize('create', Article::class);

        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        $tags = Tag::active()->orderBy('name')->get();

        return view('editor.articles.create', compact('categories', 'tags'));
    }

    /**
     * Buat kategori artikel baru langsung dari form tulis artikel (editor
     * tidak punya akses ke halaman manajemen kategori admin).
     */
    public function storeCategory(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
        ]);

        $category = ArticleCategory::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'category' => ['id' => $category->id, 'name' => $category->name],
        ]);
    }

    /**
     * Terima campuran ID tag yang sudah ada (angka) dan nama tag baru (teks,
     * diketik lewat opsi "create" di Tom Select).
     */
    private function resolveTagIds(array $tagInputs): array
    {
        $ids = [];

        foreach ($tagInputs as $input) {
            $input = trim((string) $input);

            if ($input === '') {
                continue;
            }

            if (ctype_digit($input)) {
                $ids[] = (int) $input;
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['name' => $input],
                ['is_active' => true]
            );
            $ids[] = $tag->id;
        }

        return array_unique($ids);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:150',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:articles,slug',
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            // Editor punya hak publish (lihat ArticlePolicy::publish), jadi boleh
            // langsung set status published untuk artikel sendiri.
            'status' => 'required|in:draft,archived,published',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'og_image', 'slug'])->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments');
        $data['user_id'] = Auth::id();
        $data['slug'] = $validated['slug'] ?? null;

        if ($validated['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('articles', 'public');
        }

        $article = Article::create($data);

        $tagIds = $this->resolveTagIds($request->input('tags', []));
        if (!empty($tagIds)) {
            $article->tags()->sync($tagIds);
            Tag::whereIn('id', $tagIds)->increment('usage_count');
        }

        return redirect()->route('editor.articles.index', ['tab' => 'mine'])
            ->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        $tags = Tag::active()->orderBy('name')->get();
        $article->load('tags');
        $selectedTags = $article->tags->pluck('id')->toArray();

        return view('editor.articles.edit', compact('article', 'categories', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:150',
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', \Illuminate\Validation\Rule::unique('articles', 'slug')->ignore($article->id)],
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,revision,ready_to_publish,archived,published',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'og_image', 'slug'])->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments');

        if (!empty($validated['slug'])) {
            $data['slug'] = $validated['slug'];
        }

        if ($validated['status'] === 'published' && $article->status !== 'published') {
            $data['published_at'] = now();
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
        $newTags = $this->resolveTagIds($request->input('tags', []));

        Tag::whereIn('id', array_diff($oldTags, $newTags))->decrement('usage_count');
        Tag::whereIn('id', array_diff($newTags, $oldTags))->increment('usage_count');

        $article->tags()->sync($newTags);

        return redirect()->route('editor.articles.index', ['tab' => 'mine'])
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Terbitkan draft milik siapa pun (lihat ArticlePolicy::publish) — inti
     * dari alur kerja review editor.
     */
    public function publish(Article $article)
    {
        $this->authorize('publish', $article);

        $article->update([
            'status' => 'published',
            'published_at' => $article->published_at ?? now(),
            'revision_notes' => null,
        ]);

        $this->logActivity('publish', 'articles', $article->id, null, null,
            Auth::user()->name . ' mempublikasikan artikel "' . $article->title . '"');

        return back()->with('success', 'Artikel "' . $article->title . '" berhasil dipublikasikan.');
    }

    /**
     * Tolak draft secara halus dengan mengarsipkannya (bukan menghapus) —
     * lihat ArticlePolicy::archive.
     */
    public function archive(Article $article)
    {
        $this->authorize('archive', $article);

        $article->update(['status' => 'archived']);

        $this->logActivity('archive', 'articles', $article->id, null, null,
            Auth::user()->name . ' mengarsipkan artikel "' . $article->title . '"');

        return back()->with('success', 'Artikel "' . $article->title . '" diarsipkan.');
    }

    /**
     * Kembalikan draft ke penulis dengan catatan perbaikan — penulis akan
     * melihat catatan ini dan artikel kembali berstatus draft begitu
     * disimpan ulang (lihat Author\ArticleController::update).
     */
    public function requestRevision(Request $request, Article $article)
    {
        $this->authorize('review', $article);

        $validated = $request->validate([
            'revision_notes' => 'required|string|max:1000',
        ]);

        $article->update([
            'status' => 'revision',
            'revision_notes' => $validated['revision_notes'],
        ]);

        $this->logActivity('request_revision', 'articles', $article->id, null, null,
            Auth::user()->name . ' meminta revisi artikel "' . $article->title . '"');

        return back()->with('success', 'Permintaan revisi untuk "' . $article->title . '" terkirim ke penulis.');
    }

    /**
     * Tandai draft sudah disetujui dan siap terbit — langkah antara sebelum
     * benar-benar dipublikasikan (lihat publish()).
     */
    public function markReady(Article $article)
    {
        $this->authorize('review', $article);

        $article->update([
            'status' => 'ready_to_publish',
            'revision_notes' => null,
        ]);

        $this->logActivity('mark_ready', 'articles', $article->id, null, null,
            Auth::user()->name . ' menyetujui artikel "' . $article->title . '"');

        return back()->with('success', 'Artikel "' . $article->title . '" ditandai siap terbit.');
    }
}
