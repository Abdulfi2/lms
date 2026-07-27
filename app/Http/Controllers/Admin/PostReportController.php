<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostReport;
use Illuminate\Http\Request;

class PostReportController extends Controller
{
    /**
     * Antrian laporan post forum yang menunggu tindakan admin.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $reports = PostReport::with(['post.thread.forum.course', 'post.user', 'user', 'reviewedBy'])
            ->where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.post-reports.index', compact('reports', 'status'));
    }

    /**
     * Tolak laporan (post dianggap tidak melanggar, tidak ada tindakan).
     */
    public function dismiss(PostReport $postReport)
    {
        $postReport->update([
            'status' => 'dismissed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Laporan ditolak, post tetap ditampilkan.');
    }

    /**
     * Terima laporan: hapus post yang dilaporkan dan tutup seluruh laporan terkait post tsb.
     */
    public function resolve(PostReport $postReport)
    {
        $post = $postReport->post;

        if ($post) {
            $post->delete();

            if ($post->thread) {
                $post->thread->decrement('reply_count');
                $post->thread->forum?->decrement('post_count');
            }
        }

        PostReport::where('post_id', $postReport->post_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'resolved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

        return back()->with('success', 'Post dihapus dan laporan ditutup.');
    }
}
