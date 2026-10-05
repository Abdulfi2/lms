<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $authorId = Auth::id();

        $stats = [
            'total' => Article::where('user_id', $authorId)->count(),
            'draft' => Article::where('user_id', $authorId)->where('status', 'draft')->count(),
            'published' => Article::where('user_id', $authorId)->where('status', 'published')->count(),
            'archived' => Article::where('user_id', $authorId)->where('status', 'archived')->count(),
            'views' => Article::where('user_id', $authorId)->sum('views'),
        ];

        $recentArticles = Article::where('user_id', $authorId)
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        return view('author.dashboard', compact('stats', 'recentArticles'));
    }
}
