<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    /**
     * Basis pengetahuan publik untuk swalayan pengguna sebelum membuat tiket.
     */
    public function index(Request $request)
    {
        $query = KnowledgeBaseArticle::published();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $articles = $query->latest()->paginate(12)->withQueryString();

        $categories = KnowledgeBaseArticle::published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('knowledge-base.index', compact('articles', 'categories'));
    }

    public function show(KnowledgeBaseArticle $article)
    {
        abort_unless($article->is_published, 404);

        $article->increment('views');

        return view('knowledge-base.show', compact('article'));
    }
}
