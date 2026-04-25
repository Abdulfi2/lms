<?php
// app/Http/Controllers/Student/EnrollmentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function enroll($slug)
    {
        $course = Course::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $user = auth()->user();

        // Cek apakah sudah terdaftar
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($existing) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Cek kapasitas maksimal (jika ada)
        if ($course->max_students && $course->total_students >= $course->max_students) {
            return back()->with('error', 'Kursus sudah penuh.');
        }

        try {
            DB::beginTransaction();

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_paid' => $course->final_price,
                'payment_status' => $course->final_price > 0 ? 'pending' : 'paid',
                'status' => 'active',
                'progress' => 0,
                'enrolled_at' => now(),
            ]);

            // Increment jumlah siswa di course
            $course->increment('total_students');

            DB::commit();

            // Jika berbayar, redirect ke halaman payment (opsional)
            if ($course->final_price > 0) {
                // Future: implement payment page
                // return redirect()->route('student.payments.show', $enrollment);
            }

            return redirect()->route('student.courses.show', $course->slug)
                ->with('success', 'Selamat! Anda berhasil mendaftar ke kursus ini.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }
}