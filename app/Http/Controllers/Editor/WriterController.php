<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;

class WriterController extends Controller
{
    /**
     * Direktori penulis (role author) beserta ringkasan performa artikel
     * mereka — membantu editor memantau siapa yang aktif/butuh ditindaklanjuti.
     */
    public function index(Request $request)
    {
        $query = User::role('author')->withCount([
            'articles as total_articles',
            'articles as published_articles' => fn ($q) => $q->where('status', 'published'),
            'articles as pending_articles' => fn ($q) => $q->where('status', 'draft'),
            'articles as revision_articles' => fn ($q) => $q->where('status', 'revision'),
        ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $writers = $query->withMax('articles', 'created_at')
            ->orderByDesc('articles_max_created_at')
            ->paginate(15)
            ->withQueryString();

        return view('editor.writers.index', compact('writers'));
    }
}
