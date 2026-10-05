<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Moderasi komentar yang masuk ke artikel milik author yang login.
     */
    public function index()
    {
        $comments = ArticleComment::with(['article', 'user'])
            ->whereHas('article', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->paginate(20);

        return view('author.comments.index', compact('comments'));
    }

    public function destroy(ArticleComment $comment)
    {
        abort_unless($comment->article->user_id === Auth::id(), 403);

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
