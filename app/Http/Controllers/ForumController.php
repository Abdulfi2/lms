<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Forum;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Menampilkan forum untuk suatu course.
     */
    public function index(Course $course)
    {
        $forum = Forum::where('course_id', $course->id)->first();

        if (!$forum) {
            // Buat forum otomatis jika belum ada
            $forum = Forum::create([
                'course_id' => $course->id,
                'title' => 'Forum Diskusi ' . $course->title,
                'description' => 'Diskusikan materi kursus di sini',
                'is_active' => true,
                'order' => 1,
            ]);
        }

        $threads = $forum->threads()
            ->with(['user', 'latestPost.user', 'tags'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate(20);

        return view('forums.index', compact('course', 'forum', 'threads'));
    }

    /**
     * Form buat thread baru.
     */
    public function createThread(Course $course)
    {
        $this->authorize('create threads');

        $tags = Tag::active()->get();
        return view('forums.create-thread', compact('course', 'tags'));
    }

    /**
     * Simpan thread baru.
     */
    public function storeThread(Request $request, Course $course)
    {
        $this->authorize('create threads');

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            DB::beginTransaction();

            $forum = Forum::where('course_id', $course->id)->firstOrFail();

            $thread = $forum->threads()->create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'user_id' => auth()->id(),
                'last_post_at' => now(),
                'last_post_user_id' => auth()->id(),
            ]);

            if ($request->has('tags')) {
                $thread->tags()->sync($request->tags);
            }

            // Update forum stats
            $forum->increment('thread_count');

            DB::commit();

            return redirect()->route('forums.thread.show', [$course, $thread])
                ->with('success', 'Thread berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat thread: ' . $e->getMessage());
        }
    }

    /**
     * Lihat detail thread.
     */
    public function showThread(Course $course, Thread $thread)
    {
        if ($thread->forum->course_id != $course->id) {
            abort(404);
        }

        // Increment view count
        $thread->increment('view_count');

        $posts = $thread->posts()
            ->with(['user', 'likes'])
            ->orderBy('created_at')
            ->paginate(15);

        $isLocked = $thread->is_locked;
        $canReply = !$isLocked && auth()->check() && (
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('instructor') ||
            auth()->user()->enrollments()->where('course_id', $course->id)->exists()
        );

        return view('forums.show-thread', compact('course', 'thread', 'posts', 'isLocked', 'canReply'));
    }

    /**
     * Kirim post/reply.
     */
    public function storePost(Request $request, Course $course, Thread $thread)
    {
        $this->authorize('create posts');

        if ($thread->is_locked) {
            return back()->with('error', 'Thread ini sudah dikunci, tidak dapat menambah reply.');
        }

        $request->validate([
            'content' => 'required|string|min:3',
        ]);

        try {
            DB::beginTransaction();

            $post = $thread->posts()->create([
                'user_id' => auth()->id(),
                'content' => $request->input('content'),
                'is_approved' => auth()->user()->hasRole('admin') || auth()->user()->hasRole('instructor'),
            ]);

            // Update thread
            $thread->update([
                'reply_count' => $thread->reply_count + 1,
                'last_post_at' => now(),
                'last_post_user_id' => auth()->id(),
            ]);

            // Update forum
            $thread->forum->increment('post_count');

            DB::commit();

            return redirect()->route('forums.thread.show', [$course, $thread])
                ->with('success', 'Reply berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim reply: ' . $e->getMessage());
        }
    }

    /**
     * Like/unlike post.
     */
    public function likePost(Course $course, Thread $thread, Post $post)
    {
        $this->authorize('like posts');

        $existing = \App\Models\PostLike::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('like_count');
            $liked = false;
        } else {
            \App\Models\PostLike::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);
            $post->increment('like_count');
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $post->like_count
        ]);
    }

    /**
     * Mark as solution (instructor/admin only).
     */
    public function markAsSolution(Course $course, Thread $thread, Post $post)
    {
        $this->authorize('solve threads');

        if ($thread->forum->course->instructor_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Reset solution flag on other posts
        $thread->posts()->update(['is_solution' => false]);

        // Set solution
        $post->update(['is_solution' => true]);
        $thread->update(['is_solved' => true]);

        return back()->with('success', 'Jawaban ditandai sebagai solusi.');
    }

    /**
     * Lock/unlock thread (instructor/admin only).
     */
    public function toggleLock(Course $course, Thread $thread)
    {
        $this->authorize('lock threads');

        if ($thread->forum->course->instructor_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $thread->update(['is_locked' => !$thread->is_locked]);

        $status = $thread->is_locked ? 'dikunci' : 'dibuka';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Pin/unpin thread (admin only).
     */
    public function togglePin(Course $course, Thread $thread)
    {
        $this->authorize('pin threads');

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        $status = $thread->is_pinned ? 'Dipin' : 'Unpin';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Delete post (admin or owner).
     */
    public function deletePost(Course $course, Thread $thread, Post $post)
    {
        if ($post->user_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $post->delete();

        // Update thread reply count
        $thread->decrement('reply_count');
        $thread->forum->decrement('post_count');

        return back()->with('success', 'Post berhasil dihapus.');
    }
}