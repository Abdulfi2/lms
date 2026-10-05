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

    /**
     * Buat kategori artikel baru langsung dari form tulis artikel (author
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
     * diketik lewat opsi "create" di Tom Select) — author tidak punya akses
     * ke halaman manajemen tag admin, jadi tag baru dibuat langsung di sini.
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
            // Author tidak boleh langsung publish — cuma editor/admin yang bisa (lihat ArticlePolicy::publish).
            'status' => 'required|in:draft,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            // is_featured cuma "permintaan" dari author — baru benar-benar tampil di
            // halaman utama setelah admin/editor publish artikelnya (lihat scope published()).
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'og_image', 'slug'])->all();
        $data['is_featured'] = $request->boolean('is_featured');
        // Form mengirim hidden input bernilai "0" sebelum checkbox-nya, supaya
        // unchecked tetap terkirim (checkbox polos tidak mengirim apa-apa saat unchecked).
        $data['allow_comments'] = $request->boolean('allow_comments');
        $data['user_id'] = Auth::id();
        $data['slug'] = $validated['slug'] ?? null;

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
            'subtitle' => 'nullable|string|max:150',
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', \Illuminate\Validation\Rule::unique('articles', 'slug')->ignore($article->id)],
            'category_id' => 'nullable|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => $statusRule,
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
        ]);

        $data = collect($validated)->except(['featured_image', 'tags', 'status', 'og_image', 'slug'])->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments');

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
        $newTags = $this->resolveTagIds($request->input('tags', []));

        Tag::whereIn('id', array_diff($oldTags, $newTags))->decrement('usage_count');
        Tag::whereIn('id', array_diff($newTags, $oldTags))->increment('usage_count');

        $article->tags()->sync($newTags);

        return redirect()->route('author.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }
}
