<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\Course;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Pastikan course ini benar milik instruktur yang sedang login.
     */
    private function authorizeCourse(Course $course): void
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
    }

    /**
     * Tampilkan semua forum diskusi untuk kursus yang diajar oleh instruktur.
     */
    public function index()
    {
        $instructorId = Auth::id();

        // Ambil semua forum yang terkait dengan kursus milik instruktur ini
        $forums = Forum::with(['course', 'latestThread.user'])
            ->whereHas('course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->withCount('threads')
            ->orderBy('threads_count', 'desc')
            ->get();

        // Statistik tambahan untuk dashboard forum
        $totalThreads = Thread::whereHas('forum.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->count();

        $recentThreads = Thread::with(['user', 'forum.course'])
            ->whereHas('forum.course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('instructor.forums.index', compact('forums', 'totalThreads', 'recentThreads'));
    }

    /**
     * Tampilkan daftar thread dalam satu forum (khusus kursus milik instruktur ini).
     */
    public function showForum(Course $course, Forum $forum)
    {
        $this->authorizeCourse($course);

        if ($forum->course_id !== $course->id) {
            abort(404);
        }

        $threads = $forum->threads()
            ->with(['user', 'latestPost.user', 'tags'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate(20);

        return view('instructor.forums.show-forum', compact('course', 'forum', 'threads'));
    }

    /**
     * Tampilkan detail thread beserta kontrol moderasi (lock/pin/mark solution).
     */
    public function showThread(Course $course, Forum $forum, Thread $thread)
    {
        $this->authorizeCourse($course);

        if ($thread->forum_id !== $forum->id || $forum->course_id !== $course->id) {
            abort(404);
        }

        $thread->increment('view_count');

        $posts = $thread->posts()
            ->with(['user', 'likes'])
            ->orderBy('created_at')
            ->paginate(15);

        return view('instructor.forums.show-thread', compact('course', 'forum', 'thread', 'posts'));
    }

    /**
     * Kirim balasan sebagai instruktur (otomatis approved).
     */
    public function storePost(Request $request, Course $course, Forum $forum, Thread $thread)
    {
        $this->authorizeCourse($course);

        if ($thread->is_locked) {
            return back()->with('error', 'Thread ini sudah dikunci, tidak dapat menambah balasan.');
        }

        $request->validate([
            'content' => 'required|string|min:3',
        ]);

        try {
            DB::beginTransaction();

            $thread->posts()->create([
                'user_id' => Auth::id(),
                'content' => $request->input('content'),
                'is_approved' => true,
            ]);

            $thread->update([
                'reply_count' => $thread->reply_count + 1,
                'last_post_at' => now(),
                'last_post_user_id' => Auth::id(),
            ]);

            $forum->increment('post_count');

            DB::commit();

            return redirect()->route('instructor.forums.thread.show', [$course, $forum, $thread])
                ->with('success', 'Balasan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Instructor forum reply failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim balasan. Silakan coba lagi.');
        }
    }

    /**
     * Tandai/batalkan post sebagai solusi thread.
     */
    public function markAsSolution(Course $course, Forum $forum, Thread $thread, Post $post)
    {
        $this->authorizeCourse($course);

        $thread->posts()->update(['is_solution' => false]);
        $post->update(['is_solution' => true]);
        $thread->update(['is_solved' => true]);

        return back()->with('success', 'Balasan ditandai sebagai solusi.');
    }

    /**
     * Kunci/buka thread.
     */
    public function toggleLock(Course $course, Forum $forum, Thread $thread)
    {
        $this->authorizeCourse($course);

        $thread->update(['is_locked' => !$thread->is_locked]);

        $status = $thread->is_locked ? 'dikunci' : 'dibuka';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Pin/unpin thread.
     */
    public function togglePin(Course $course, Forum $forum, Thread $thread)
    {
        $this->authorizeCourse($course);

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        $status = $thread->is_pinned ? 'disematkan' : 'dilepas dari sematan';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Hapus post yang melanggar aturan (moderasi instruktur).
     */
    public function deletePost(Course $course, Forum $forum, Thread $thread, Post $post)
    {
        $this->authorizeCourse($course);

        $post->delete();

        $thread->decrement('reply_count');
        $forum->decrement('post_count');

        return back()->with('success', 'Post berhasil dihapus.');
    }
}
