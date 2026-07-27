<?php
// app/Http/Controllers/Admin/FailedJobsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FailedJobsController extends Controller
{
    /**
     * Display list of failed jobs
     */
    public function index()
    {
        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->paginate(20);

        return view('admin.failed-jobs.index', compact('failedJobs'));
    }

    /**
     * Retry a specific failed job.
     * Note: `queue:retry` is only available as an Artisan command, not a Queue facade method.
     */
    public function retry($id)
    {
        if (!DB::table('failed_jobs')->where('id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Job tidak ditemukan.'], 404);
        }

        Artisan::call('queue:retry', ['id' => [$id]]);

        return response()->json(['success' => true, 'message' => 'Job berhasil di-retry.']);
    }

    /**
     * Retry all failed jobs
     */
    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return response()->json(['success' => true, 'message' => 'Semua job gagal di-retry.']);
    }

    /**
     * Delete a specific failed job
     */
    public function delete($id)
    {
        $deleted = DB::table('failed_jobs')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Job tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Job berhasil dihapus.']);
    }

    /**
     * Delete all failed jobs
     */
    public function deleteAll()
    {
        DB::table('failed_jobs')->truncate();

        return response()->json(['success' => true, 'message' => 'Semua job gagal dihapus.']);
    }

    /**
     * Get job details
     */
    public function show($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if (!$job) {
            abort(404, 'Failed job tidak ditemukan.');
        }

        // Decode payload untuk melihat detail
        $payload = json_decode($job->payload, true);

        return view('admin.failed-jobs.show', compact('job', 'payload'));
    }
}
