<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBaseArticle::with('user');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $articles = $query->latest()->paginate(15)->withQueryString();

        return view('support.knowledge-base.index', compact('articles'));
    }

    public function create()
    {
        return view('support.knowledge-base.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);

        KnowledgeBaseArticle::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'content' => $validated['content'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('support.knowledge-base.index')
            ->with('success', 'Artikel basis pengetahuan berhasil disimpan.');
    }

    public function edit(KnowledgeBaseArticle $knowledgeBaseArticle)
    {
        return view('support.knowledge-base.edit', ['article' => $knowledgeBaseArticle]);
    }

    public function update(Request $request, KnowledgeBaseArticle $knowledgeBaseArticle)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);

        $knowledgeBaseArticle->update([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'content' => $validated['content'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('support.knowledge-base.index')
            ->with('success', 'Artikel basis pengetahuan berhasil diperbarui.');
    }

    public function destroy(KnowledgeBaseArticle $knowledgeBaseArticle)
    {
        $knowledgeBaseArticle->delete();

        return back()->with('success', 'Artikel basis pengetahuan dihapus.');
    }
}
