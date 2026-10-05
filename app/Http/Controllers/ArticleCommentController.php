<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        abort_unless($article->allow_comments, 403, 'Komentar dinonaktifkan untuk artikel ini.');

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $article->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Hapus komentar — boleh dilakukan oleh pemilik komentar sendiri, penulis
     * artikelnya (moderasi), atau admin.
     */
    public function destroy(Article $article, ArticleComment $comment)
    {
        abort_unless($comment->article_id === $article->id, 404);

        $user = Auth::user();
        $canDelete = $comment->user_id === $user->id
            || $article->user_id === $user->id
            || $user->hasRole('admin');

        abort_unless($canDelete, 403);

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
