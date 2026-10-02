<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\QuizAttempt;
use App\Jobs\GenerateCertificateJob;
use App\Models\UserPoint;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    /**
     * Display lesson content.
     */
    public function show(Course $course, Lesson $lesson)
    {
        // Ensure lesson belongs to course
        if ($lesson->section->course_id != $course->id) {
            abort(404);
        }

        // Check enrollment
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        if ($enrollment->payment_status !== 'paid') {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('error', 'Selesaikan pembayaran terlebih dahulu untuk mengakses materi ini.');
        }

        // Get all lessons for navigation
        $allLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.*')
            ->get();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1)
            ? $allLessons[$currentIndex + 1]
            : null;

        // Check completion status
        $isCompleted = LessonCompletion::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->exists();

        // Get lesson resources
        $resources = $lesson->resources()->orderBy('order')->get();

        // Get user progress
        $userPoint = UserPoint::firstOrCreate(['user_id' => auth()->id()]);

        // Pretest & posttest yang dikaitkan ke lesson ini (kalau lesson bertipe "quiz" dan
        // sudah dihubungkan lewat form instruktur, dibedakan lewat quiz_type).
        // - Pretest: wajib dikerjakan dulu sebelum konten materi lesson terbuka.
        // - Posttest: syarat lesson dianggap selesai (menggantikan tombol "Tandai Selesai" manual).
        $pretest = $this->loadLessonQuizForStudent($lesson, 'pretest');
        $posttest = $this->loadLessonQuizForStudent($lesson, 'posttest');

        // orderByDesc('id') dipakai (bukan latest()/created_at) supaya urutan attempt tetap
        // pasti walau dua attempt tercatat dalam detik yang sama (created_at bisa kembar).
        $pretestAttempt = $pretest
            ? QuizAttempt::where('quiz_id', $pretest->id)->where('user_id', auth()->id())->where('status', 'completed')->orderByDesc('id')->first()
            : null;
        // Konten terkunci kalau lesson punya pretest dan siswa belum pernah menyelesaikannya
        // (tidak perlu lulus — pretest sifatnya diagnostik, bukan gate berdasarkan nilai).
        $contentLocked = $pretest && !$pretestAttempt;

        $posttestAttempt = $posttest
            ? QuizAttempt::where('quiz_id', $posttest->id)->where('user_id', auth()->id())->where('status', 'completed')->orderByDesc('id')->first()
            : null;
        // "Pernah lulus" (bukan cuma attempt terakhir) — sekali lulus, tetap dianggap lulus
        // walau nanti iseng dicoba ulang dan hasilnya lebih jelek.
        $posttestPassed = $posttest && QuizAttempt::where('quiz_id', $posttest->id)
            ->where('user_id', auth()->id())
            ->where('is_passed', true)
            ->exists();

        // Breadcrumb: lesson-lesson lain di section yang sama hanya bisa diklik kalau
        // lesson SEBELUMNYA (berurutan, mengikuti $allLessons di atas) sudah completed —
        // mencegah siswa "loncat" lewat breadcrumb ke materi yang belum waktunya dibuka.
        $completedIds = LessonCompletion::where('user_id', auth()->id())
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->pluck('lesson_id')
            ->all();

        $blocked = false;
        $lockedMap = [];
        foreach ($allLessons as $l) {
            $lockedMap[$l->id] = $blocked;
            if (!in_array($l->id, $completedIds, true)) {
                $blocked = true;
            }
        }

        $sectionLessonsForBreadcrumb = $lesson->section->lessons()->orderBy('order')->get()
            ->map(fn ($l) => [
                'label' => $l->title,
                'url' => route('student.lessons.show', [$course, $l]),
                'locked' => $lockedMap[$l->id] ?? false,
                'active' => $l->id === $lesson->id,
            ]);

        return view('student.lessons.show', compact(
            'course',
            'lesson',
            'prevLesson',
            'nextLesson',
            'isCompleted',
            'resources',
            'enrollment',
            'userPoint',
            'pretest',
            'pretestAttempt',
            'contentLocked',
            'posttest',
            'posttestAttempt',
            'posttestPassed',
            'sectionLessonsForBreadcrumb'
        ));
    }

    /**
     * Ambil quiz (pretest/posttest) milik lesson beserta info percobaan siswa saat ini,
     * atau null kalau lesson tidak punya quiz tipe tsb / belum dipublikasikan.
     */
    private function loadLessonQuizForStudent(Lesson $lesson, string $quizType)
    {
        if ($lesson->type !== 'quiz') {
            return null;
        }

        $quiz = $quizType === 'pretest' ? $lesson->pretest : $lesson->posttest;
        if (!$quiz || !$quiz->is_published) {
            return null;
        }

        $quiz->loadCount('questions');
        $quiz->user_attempt_count = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->count();
        $quiz->user_best_score = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->max('percentage') ?? 0;

        return $quiz;
    }

    /**
     * Mark lesson as completed.
     */
    public function complete(Request $request, Lesson $lesson)
    {
        try {
            // Kalau lesson punya posttest, penyelesaian lesson HARUS lewat lulus posttest
            // (lihat QuizController::updateLessonProgress), bukan tombol manual ini —
            // supaya siswa tidak bisa melewati posttest begitu saja.
            if ($lesson->type === 'quiz' && $lesson->posttest()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson ini punya post-test. Selesaikan dan lulus post-test untuk menandai lesson ini selesai.'
                ], 422);
            }

            // Cegah selesaikan lesson yang kontennya masih terkunci pretest (mis. request
            // API langsung tanpa lewat UI yang sudah menyembunyikan tombolnya).
            if ($lesson->type === 'quiz') {
                $pretest = $lesson->pretest;
                if ($pretest && $pretest->is_published) {
                    $pretestDone = QuizAttempt::where('quiz_id', $pretest->id)
                        ->where('user_id', auth()->id())
                        ->where('status', 'completed')
                        ->exists();
                    if (!$pretestDone) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Kerjakan pre-test lesson ini terlebih dahulu.'
                        ], 422);
                    }
                }
            }

            // Check lesson and section relationship
            if (!$lesson->section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lesson tidak valid (section tidak ditemukan)'
                ], 400);
            }

            // Get course from lesson
            $course = $lesson->section->course;
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus tidak ditemukan untuk lesson ini'
                ], 400);
            }

            // Check if user is authenticated
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Check enrollment
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar di kursus ini'
                ], 403);
            }

            if (!in_array($enrollment->status, ['active', 'completed']) || ($enrollment->expires_at && $enrollment->expires_at->isPast())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Anda ke kursus ini sudah tidak aktif'
                ], 403);
            }

            if ($enrollment->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selesaikan pembayaran terlebih dahulu untuk mengakses kursus ini'
                ], 402);
            }

            // ==================== PREVENT DUPLICATE ====================

            $alreadyCompleted = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->exists();

            if ($alreadyCompleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson sudah selesai sebelumnya',
                    'progress' => round($enrollment->progress)
                ]);
            }

            // ==================== SAVE COMPLETION ====================

            DB::beginTransaction();

            // Create lesson completion record
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $request->input('time_spent', 0),
                'watch_percentage' => $request->input('watch_percentage', 100),
                'last_position' => $request->input('last_position', 0),
            ]);

            // Calculate total lessons in course
            $totalLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                ->where('sections.course_id', $course->id)
                ->count();

            // Calculate completed lessons
            $completedLessons = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
                    ->where('sections.course_id', $course->id)
                    ->select('lessons.id'))
                ->count();

            // Calculate progress percentage
            $progress = ($completedLessons / max($totalLessons, 1)) * 100;
            $progress = round($progress, 2);

            // Update enrollment progress
            $wasCompleted = $enrollment->progress >= 100;
            $isNowCompleted = $progress >= 100;

            $enrollment->update([
                'progress' => $progress,
                'status' => $isNowCompleted ? 'completed' : $enrollment->status,
                'completed_at' => $isNowCompleted && !$wasCompleted ? now() : $enrollment->completed_at,
            ]);

            DB::commit();

            // ==================== GAMIFICATION - UPDATE POINTS EVERY LESSON ====================
            // Update daily streak
            GamificationService::updateStreak($user);

            // Add points for completing this lesson
            $lessonPoints = $lesson->points ?? 10;
            GamificationService::addPoints($user, $lessonPoints, "Menyelesaikan lesson: {$lesson->title} (+{$lessonPoints} poin)");

            // Check badges and achievements after earning points
            GamificationService::checkBadges($user);
            GamificationService::checkAchievements($user);

            // ==================== GENERATE CERTIFICATE ====================

            $certificateGenerated = false;
            if ($isNowCompleted && !$wasCompleted && !$enrollment->certificate_issued_at) {
                try {
                    // Update certificate issued timestamp
                    $enrollment->certificate_issued_at = now();
                    $enrollment->saveQuietly();

                    // Dispatch job to generate certificate (sync for immediate result)
                    GenerateCertificateJob::dispatchSync($enrollment);
                    $certificateGenerated = true;

                    // Add bonus points for completing course
                    GamificationService::addPoints($user, 100, "Menyelesaikan kursus: {$course->title} (+100 bonus poin)");
                    GamificationService::courseCompleted($user, $course);

                    Log::info('Certificate generation dispatched', [
                        'enrollment_id' => $enrollment->id,
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'progress' => $progress
                    ]);
                } catch (\Exception $e) {
                    Log::error('Certificate generation failed: ' . $e->getMessage(), [
                        'enrollment_id' => $enrollment->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // ==================== RESPONSE ====================

            $message = 'Lesson selesai! +' . $lessonPoints . ' poin';
            if ($isNowCompleted && !$wasCompleted) {
                $message = $certificateGenerated
                    ? 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat telah dibuat! +100 bonus poin'
                    : 'Selamat! Anda telah menyelesaikan kursus ini. Sertifikat sedang diproses. +100 bonus poin';
            }

            // Get next lesson URL
            $nextLessonUrl = null;
            $nextLesson = $this->getNextLesson($course, $lesson);
            if ($nextLesson) {
                $nextLessonUrl = route('student.lessons.show', [$course, $nextLesson]);
            }

            // Get updated user points
            $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'progress' => $progress,
                'completed' => $isNowCompleted,
                'certificate_generated' => $certificateGenerated,
                'points_earned' => $lessonPoints,
                'total_points' => $userPoint->total_points,
                'current_level' => $userPoint->current_level,
                'next_lesson_url' => $nextLessonUrl
            ]);

        } catch (\Exception $e) {
            // DB::beginTransaction() hanya terjadi di pertengahan method ini, dan sudah di-commit
            // sebelum blok gamifikasi/sertifikat berjalan — exception dari kode sebelum begin atau
            // setelah commit tidak punya transaksi aktif untuk di-rollback ("There is no active
            // transaction"), yang tadinya menutupi pesan error asli di atas.
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Lesson completion error: ' . $e->getMessage(), [
                'lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan progress: ' . $e->getMessage()
            ], 500);
        }
    }

    public function trackTime(Request $request, Lesson $lesson)
    {
        try {
            // Validasi apakah user terdaftar di course
            $course = $lesson->section->course;
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->where('payment_status', 'paid')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists();

            if (!$enrollment) {
                return response()->json(['success' => false], 403);
            }

            // Simpan atau update waktu belajar
            $completion = LessonCompletion::firstOrCreate(
                ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
                ['time_spent' => 0]
            );

            $completion->increment('time_spent', $request->input('time_spent', 0));

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get next lesson URL for auto-redirect.
     */
    /**
     * Get the next unfinished lesson in course.
     */
    private function getNextLesson($course, $currentLesson)
    {
        // Ambil semua lesson dalam course dengan urutan yang benar
        $allLessons = Lesson::join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order', 'asc')
            ->orderBy('lessons.order', 'asc')
            ->select('lessons.*', 'sections.order as section_order')
            ->get();

        // Cari lesson yang belum selesai setelah current lesson
        $foundCurrent = false;
        foreach ($allLessons as $lessonItem) {
            if ($foundCurrent) {
                // Cek apakah lesson ini sudah selesai?
                $isCompleted = LessonCompletion::where('user_id', auth()->id())
                    ->where('lesson_id', $lessonItem->id)
                    ->exists();

                if (!$isCompleted) {
                    return $lessonItem;
                }
            }

            if ($lessonItem->id == $currentLesson->id) {
                $foundCurrent = true;
            }
        }

        return null;
    }

}