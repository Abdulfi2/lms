<?php
// app/Http/Controllers/Student/EnrollmentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function enroll(Request $request, $slug)
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

        // Cek batas waktu pendaftaran & periode kursus
        if ($course->enrollment_end_date && now()->greaterThan($course->enrollment_end_date)) {
            return back()->with('error', 'Batas waktu pendaftaran kursus ini sudah berakhir.');
        }

        if ($course->end_date && now()->greaterThan($course->end_date)) {
            return back()->with('error', 'Kursus ini sudah berakhir.');
        }

        // Kupon diskon (opsional, hanya relevan untuk kursus berbayar)
        $coupon = null;
        $discountAmount = 0;
        $finalAmount = $course->final_price;

        if ($request->filled('coupon_code') && $course->final_price > 0) {
            $coupon = Coupon::whereRaw('LOWER(code) = ?', [strtolower($request->input('coupon_code'))])->first();

            if (!$coupon) {
                return back()->with('error', 'Kode kupon tidak ditemukan.');
            }

            $error = $coupon->validationError($course, $course->final_price);
            if ($error) {
                return back()->with('error', $error);
            }

            $discountAmount = $coupon->calculateDiscount($course->final_price);
            $finalAmount = max(0, $course->final_price - $discountAmount);
        }

        try {
            DB::beginTransaction();

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'coupon_id' => $coupon?->id,
                'amount_paid' => $finalAmount,
                'discount_amount' => $discountAmount,
                'payment_status' => $finalAmount > 0 ? 'pending' : 'paid',
                'status' => 'active',
                'progress' => 0,
                'enrolled_at' => now(),
            ]);

            // Increment jumlah siswa di course
            $course->increment('total_students');

            if ($coupon) {
                $coupon->increment('used_count');
            }

            DB::commit();

            // Jika berbayar, akses materi akan diblokir sampai payment_status diubah menjadi 'paid'
            // (lihat CourseController/LessonController) — belum ada payment gateway terpasang.
            if ($finalAmount > 0) {
                return redirect()->route('student.courses.show', $course->slug)
                    ->with('info', 'Pendaftaran tercatat. Selesaikan pembayaran untuk mengakses materi kursus.');
            }

            return redirect()->route('student.courses.show', $course->slug)
                ->with('success', 'Selamat! Anda berhasil mendaftar ke kursus ini.');

        } catch (QueryException $e) {
            DB::rollBack();

            if ((int) $e->getCode() === 23000) {
                return redirect()->route('student.courses.show', $course->slug)
                    ->with('info', 'Anda sudah terdaftar di kursus ini.');
            }

            \Log::error('Enrollment failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftar. Silakan coba lagi.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Enrollment failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftar. Silakan coba lagi.');
        }
    }
}