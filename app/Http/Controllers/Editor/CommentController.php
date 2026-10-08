<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;

class CommentController extends Controller
{
    /**
     * Moderasi komentar di seluruh artikel sistem (bukan cuma milik editor
     * sendiri) — cakupan editor lebih luas daripada author.
     */
    public function index()
    {
        $comments = ArticleComment::with(['article', 'user'])
            ->latest()
            ->paginate(20);

        return view('editor.comments.index', compact('comments'));
    }

    public function destroy(ArticleComment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
