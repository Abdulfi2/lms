<?php
// app/Http/Controllers/Admin/FailedJobsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
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
     * Retry a specific failed job
     */
    public function retry($id)
    {
        Queue::retry($id);

        return redirect()->back()->with('success', 'Job berhasil di-retry.');
    }

    /**
     * Retry all failed jobs
     */
    public function retryAll()
    {
        Queue::retryAll();

        return redirect()->back()->with('success', 'Semua job gagal di-retry.');
    }

    /**
     * Delete a specific failed job
     */
    public function delete($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Job berhasil dihapus.');
    }

    /**
     * Delete all failed jobs
     */
    public function deleteAll()
    {
        DB::table('failed_jobs')->truncate();

        return redirect()->back()->with('success', 'Semua job gagal dihapus.');
    }

    /**
     * Get job details
     */
    public function show($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        // Decode payload untuk melihat detail
        $payload = json_decode($job->payload, true);

        return view('admin.failed-jobs.show', compact('job', 'payload'));
    }
}