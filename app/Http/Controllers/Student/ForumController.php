<?php
// app/Http/Controllers/Student/ForumController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Menampilkan daftar forum per course yang diikuti student.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil course yang diikuti student
        $enrolledCourseIds = $user->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        // Ambil forum berdasarkan course yang diikuti
        $forums = Forum::with(['course', 'latestThread'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->orderBy('order')
            ->paginate(10);

        // Statistik
        $totalThreads = Thread::whereIn('forum_id', $forums->pluck('id'))->count();
        $totalPosts = Post::whereIn('forum_id', $forums->pluck('id'))->count();
        $myThreads = Thread::where('user_id', $user->id)
            ->whereIn('forum_id', $forums->pluck('id'))
            ->count();
        $myPosts = Post::where('user_id', $user->id)
            ->whereIn('forum_id', $forums->pluck('id'))
            ->count();

        return view('student.forums.index', compact('forums', 'totalThreads', 'totalPosts', 'myThreads', 'myPosts'));
    }

    /**
     * Menampilkan thread dalam suatu forum.
     */
    public function showForum(Course $course, Forum $forum)
    {
        // Cek apakah student terdaftar di course ini
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        $threads = $forum->threads()
            ->with(['user', 'latestPost.user', 'tags'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate(20);

        $tags = Tag::active()->get();

        return view('student.forums.threads', compact('course', 'forum', 'threads', 'tags'));
    }

    /**
     * Form buat thread baru.
     */
    public function createThread(Course $course, Forum $forum)
    {
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        $tags = Tag::active()->get();

        return view('student.forums.create-thread', compact('course', 'forum', 'tags'));
    }

    /**
     * Simpan thread baru.
     */
    public function storeThread(Request $request, Course $course, Forum $forum)
    {
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Anda tidak terdaftar di kursus ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            DB::beginTransaction();

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

            return redirect()->route('student.forums.thread.show', [$course, $forum, $thread])
                ->with('success', 'Thread berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat thread: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail thread beserta posts.
     */
    public function showThread(Course $course, Forum $forum, Thread $thread)
    {
        // Cek apakah student terdaftar
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        // Increment view count
        $thread->increment('view_count');

        $posts = $thread->posts()
            ->with(['user', 'likes'])
            ->orderBy('created_at')
            ->paginate(15);

        $isLocked = $thread->is_locked;
        $canReply = !$isLocked;

        // Cek apakah user sudah like post tertentu (untuk UI)
        $likedPosts = [];
        if (auth()->check()) {
            $likedPosts = PostLike::where('user_id', auth()->id())
                ->whereIn('post_id', $posts->pluck('id'))
                ->pluck('post_id')
                ->toArray();
        }

        return view('student.forums.show-thread', compact('course', 'forum', 'thread', 'posts', 'isLocked', 'canReply', 'likedPosts'));
    }

    /**
     * Menyimpan reply/post ke thread.
     */
    public function storePost(Request $request, Course $course, Forum $forum, Thread $thread)
    {
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Anda tidak terdaftar di kursus ini.');
        }

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
                'is_approved' => true, // Auto approve untuk student
            ]);

            // Update thread
            $thread->update([
                'reply_count' => $thread->reply_count + 1,
                'last_post_at' => now(),
                'last_post_user_id' => auth()->id(),
            ]);

            // Update forum post count
            $forum->increment('post_count');

            DB::commit();

            return redirect()->route('student.forums.thread.show', [$course, $forum, $thread])
                ->with('success', 'Reply berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim reply: ' . $e->getMessage());
        }
    }

    /**
     * Like/unlike post (AJAX).
     */
    public function likePost(Course $course, Forum $forum, Thread $thread, Post $post)
    {
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $existing = PostLike::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('like_count');
            $liked = false;
        } else {
            PostLike::create([
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
     * Edit thread (hanya oleh pembuat thread).
     */
    public function editThread(Course $course, Forum $forum, Thread $thread)
    {
        if ($thread->user_id !== auth()->id()) {
            abort(403, 'Anda hanya bisa mengedit thread sendiri.');
        }

        $tags = Tag::active()->get();
        $selectedTags = $thread->tags->pluck('id')->toArray();

        return view('student.forums.edit-thread', compact('course', 'forum', 'thread', 'tags', 'selectedTags'));
    }

    /**
     * Update thread.
     */
    public function updateThread(Request $request, Course $course, Forum $forum, Thread $thread)
    {
        if ($thread->user_id !== auth()->id()) {
            return back()->with('error', 'Anda hanya bisa mengedit thread sendiri.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            DB::beginTransaction();

            $thread->update([
                'title' => $request->title,
                'content' => $request->input('content'),
            ]);

            $thread->tags()->sync($request->tags ?? []);

            DB::commit();

            return redirect()->route('student.forums.thread.show', [$course, $forum, $thread])
                ->with('success', 'Thread berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui thread: ' . $e->getMessage());
        }
    }

    /**
     * Hapus post (hanya oleh pembuat post atau admin).
     */
    public function deletePost(Course $course, Forum $forum, Thread $thread, Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus post ini.');
        }

        $post->delete();

        // Update thread reply count
        $thread->decrement('reply_count');
        $forum->decrement('post_count');

        return back()->with('success', 'Post berhasil dihapus.');
    }
}