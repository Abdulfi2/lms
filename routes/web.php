<?php

use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Author\DashboardController as AuthorDashboardController;
use App\Http\Controllers\Author\ArticleController as AuthorArticleController;
use App\Http\Controllers\Editor\DashboardController as EditorDashboardController;
use App\Http\Controllers\Editor\ArticleController as EditorArticleController;
use App\Http\Controllers\Editor\CategoryController as EditorCategoryController;
use App\Http\Controllers\Editor\CommentController as EditorCommentController;
use App\Http\Controllers\Editor\WriterController as EditorWriterController;
use App\Http\Controllers\Editor\CalendarController as EditorCalendarController;
use App\Http\Controllers\Support\DashboardController as SupportDashboardController;
use App\Http\Controllers\Support\UserController as SupportUserController;
use App\Http\Controllers\Support\CourseController as SupportCourseController;
use App\Http\Controllers\Support\EnrollmentController as SupportEnrollmentController;
use App\Http\Controllers\Support\TicketController as SupportTicketController;
use App\Http\Controllers\Support\KnowledgeBaseController as SupportKnowledgeBaseController;
use App\Http\Controllers\Support\ScheduleController as SupportScheduleController;
use App\Http\Controllers\Support\ReportController as SupportReportController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\BadgeController as AdminBadgeController;
use App\Http\Controllers\Admin\LevelController as AdminLevelController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\GoogleMailController;
use App\Http\Controllers\Admin\WishlistController as AdminWishlistController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\PostReportController as AdminPostReportController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\Author\CategoryController as AuthorCategoryController;
use App\Http\Controllers\Author\CommentController as AuthorCommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\Instructor\PublicProfileController as InstructorPublicProfileController;
use App\Http\Controllers\CourseController as PublicCourseController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventDashboardController;
use App\Http\Controllers\Admin\FailedJobsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\SectionController as InstructorSectionController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\LessonResourceController;
use App\Http\Controllers\Instructor\AssignmentController as InstructorAssignmentController;
use App\Http\Controllers\Instructor\StudentController;
use App\Http\Controllers\Instructor\SubmissionController;
use App\Http\Controllers\Instructor\ReviewController as InstructorReviewController;
use App\Http\Controllers\Instructor\AnalyticsController as InstructorAnalyticsController;
use App\Http\Controllers\Instructor\EarningsController as InstructorEarningsController;
use App\Http\Controllers\Instructor\ForumController as InstructorForumController;
use App\Http\Controllers\Student\AchievementController;
use App\Http\Controllers\Student\AssignmentController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ReviewController as StudentReviewController;
use App\Http\Controllers\Student\ForumController as StudentForumController;
use App\Http\Controllers\Student\EventController as StudentEventController;
use App\Http\Controllers\Student\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Public Event Routes (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{article}/share', [ArticleController::class, 'share'])->name('articles.share');

Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::post('/events/{slug}/register', [PublicEventController::class, 'register'])->name('events.register');

Route::get('/certificate/verify/{code}', [CertificateController::class, 'verify'])->name('certificate.verify');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

Route::get('/instructors/{instructor}', [InstructorProfileController::class, 'show'])->name('instructors.show');

Route::view('/about', 'public.about')->name('about');
Route::view('/faq', 'public.faq')->name('faq');
Route::view('/privacy-policy', 'public.privacy-policy')->name('privacy-policy');
Route::view('/terms', 'public.terms')->name('terms');

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect berdasarkan role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        } elseif ($user->hasRole('event_manager')) {
            return redirect()->route('admin.events.dashboard');
        } elseif ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        } elseif ($user->hasRole('editor')) {
            return redirect()->route('editor.dashboard');
        } elseif ($user->hasRole('support')) {
            return redirect()->route('support.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [PublicCourseController::class, 'show'])->name('courses.show');

    Route::post('/articles/{article}/like', [ArticleController::class, 'toggleLike'])->name('articles.like');
    Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store'])->name('articles.comments.store');
    Route::delete('/articles/{article}/comments/{comment}', [ArticleCommentController::class, 'destroy'])->name('articles.comments.destroy');

    // Tiket bantuan — siapa saja yang login boleh membuat & membalas tiket
    // miliknya sendiri. Penanganan tiket oleh staf ada di grup role:support.
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Basis pengetahuan publik (swalayan sebelum membuat tiket).
    Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::get('/knowledge-base/{article}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');

    /*
    |--------------------------------------------------------------------------
    | Admin Event Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin|event_manager'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard event manager (path dibuat 'event-dashboard' agar tidak bentrok dengan /events/{event})
        Route::get('/event-dashboard', [EventDashboardController::class, 'index'])->name('events.dashboard');

        // Event Management
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Additional routes
        Route::patch('/events/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('events.toggle-status');

        // Event Registration Management
        Route::get('/events/{event}/registrations', [EventController::class, 'registrations'])->name('events.registrations');
        Route::patch('/events/registrations/{registration}/status', [EventController::class, 'updateRegistrationStatus'])->name('events.registrations.update-status');
        Route::post('/events/{event}/registrations/bulk-update', [EventController::class, 'bulkUpdateRegistration'])->name('events.registrations.bulk-update');

        // Export
        Route::get('/events/{event}/export', [EventController::class, 'exportRegistrations'])->name('events.export');

        // Event Utilities
        Route::post('/events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');

        // API Stats
        Route::get('/events-stats', [EventController::class, 'getStats'])->name('events.stats');
    });

    // Fitur yang dipakai bareng admin & support (lihat role:support di bawah) —
    // read-only/terbatas, aman dibagikan langsung tanpa controller terpisah.
    Route::middleware(['auth', 'role:admin|support'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');

        Route::get('/post-reports', [AdminPostReportController::class, 'index'])->name('post-reports.index');
        Route::patch('/post-reports/{postReport}/dismiss', [AdminPostReportController::class, 'dismiss'])->name('post-reports.dismiss');
        Route::delete('/post-reports/{postReport}/resolve', [AdminPostReportController::class, 'resolve'])->name('post-reports.resolve');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('users/{user}/approval-status', [UserController::class, 'updateApprovalStatus'])->name('users.approval-status');

        // Role Management
        Route::get('/roles', [RolePermissionController::class, 'roles'])->name('roles.index');
        Route::get('/roles/create', [RolePermissionController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

        // Permission Management
        Route::get('/permissions', [RolePermissionController::class, 'permissions'])->name('permissions.index');
        Route::get('/permissions/create', [RolePermissionController::class, 'createPermission'])->name('permissions.create');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('permissions.edit');
        Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');

        // Articles
        Route::post('articles/upload-image', [AdminArticleController::class, 'uploadImage'])->name('articles.upload-image');
        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::patch('articles/{article}/toggle-status', [AdminArticleController::class, 'toggleStatus'])->name('articles.toggle-status');
        Route::resource('article-categories', ArticleCategoryController::class)->except(['show']);

        // Categories & Tags
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::resource('tags', TagController::class)->except(['show']);
        Route::patch('tags/{tag}/toggle-status', [TagController::class, 'toggleStatus'])->name('tags.toggle-status');

        // Course
        Route::resource('courses', CourseController::class);
        Route::patch('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
        Route::patch('courses/{course}/approve', [CourseController::class, 'approve'])->name('courses.approve');
        Route::patch('courses/{course}/reject', [CourseController::class, 'reject'])->name('courses.reject');

        Route::prefix('courses/{course}')->group(function () {
            Route::resource('sections', SectionController::class)->except(['show']);
            Route::post('sections/update-order', [SectionController::class, 'updateOrder'])->name('courses.sections.update-order');

            Route::prefix('sections/{section}')->group(function () {
                Route::resource('lessons', LessonController::class)->except(['show']);
                Route::post('lessons/update-order', [LessonController::class, 'updateOrder'])->name('courses.sections.lessons.update-order');
            });
        });

        // Konfirmasi pembayaran manual (belum ada payment gateway)
        Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::patch('/enrollments/{enrollment}/mark-paid', [AdminEnrollmentController::class, 'markPaid'])->name('enrollments.mark-paid');
        Route::post('/enrollments/{enrollment}/refund', [AdminEnrollmentController::class, 'refund'])->name('enrollments.refund');

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Manajemen Sertifikat
        Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
        Route::patch('/certificates/{certificate}/toggle-verified', [AdminCertificateController::class, 'toggleVerified'])->name('certificates.toggle-verified');
        Route::delete('/certificates/{certificate}', [AdminCertificateController::class, 'destroy'])->name('certificates.destroy');

        // Gamifikasi: Achievement, Badge, Level
        Route::resource('achievements', AdminAchievementController::class)->except(['show']);
        Route::patch('achievements/{achievement}/toggle-status', [AdminAchievementController::class, 'toggleStatus'])->name('achievements.toggle-status');
        Route::resource('badges', AdminBadgeController::class)->except(['show']);
        Route::patch('badges/{badge}/toggle-status', [AdminBadgeController::class, 'toggleStatus'])->name('badges.toggle-status');
        Route::resource('levels', AdminLevelController::class)->except(['show']);

        // Activity Log (read-only)
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

        // Site Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/google/disconnect', [GoogleMailController::class, 'disconnect'])->name('settings.google.disconnect');

        // Statistik Wishlist
        Route::get('/wishlists', [AdminWishlistController::class, 'index'])->name('wishlists.index');

        // Instructor Payout
        Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
        Route::get('/payouts/{instructor}', [AdminPayoutController::class, 'show'])->name('payouts.show');
        Route::post('/payouts/{instructor}', [AdminPayoutController::class, 'store'])->name('payouts.store');
        Route::delete('/payouts/entry/{payout}', [AdminPayoutController::class, 'destroy'])->name('payouts.destroy');
        Route::patch('/payouts/entry/{payout}/approve', [AdminPayoutController::class, 'approve'])->name('payouts.approve');
        Route::patch('/payouts/entry/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');

        // Coupon / Diskon
        Route::resource('coupons', AdminCouponController::class)->except(['show']);
        Route::patch('coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

        // Broadcast Notifikasi / Pengumuman
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');

        // Inbox Pesan Kontak
        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::post('/contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'reply'])->name('contact-messages.reply');
        Route::patch('/contact-messages/{contactMessage}/close', [AdminContactMessageController::class, 'close'])->name('contact-messages.close');
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');

        // Failed Jobs Management
        Route::get('/failed-jobs', [FailedJobsController::class, 'index'])->name('failed-jobs.index');
        Route::get('/failed-jobs/{id}', [FailedJobsController::class, 'show'])->name('failed-jobs.show');
        Route::post('/failed-jobs/{id}/retry', [FailedJobsController::class, 'retry'])->name('failed-jobs.retry');
        Route::post('/failed-jobs/retry-all', [FailedJobsController::class, 'retryAll'])->name('failed-jobs.retry-all');
        Route::delete('/failed-jobs/{id}', [FailedJobsController::class, 'delete'])->name('failed-jobs.delete');
        Route::delete('/failed-jobs', [FailedJobsController::class, 'deleteAll'])->name('failed-jobs.delete-all');
    });

    // Author — kelola artikel milik sendiri saja (draft/archived). Publish & hapus
    // tetap wewenang editor/admin, lihat App\Policies\ArticlePolicy.
    Route::middleware(['role:author'])->prefix('author')->name('author.')->group(function () {
        Route::get('/dashboard', [AuthorDashboardController::class, 'index'])->name('dashboard');
        Route::post('articles/upload-image', [AuthorArticleController::class, 'uploadImage'])->name('articles.upload-image');
        Route::post('article-categories', [AuthorArticleController::class, 'storeCategory'])->name('article-categories.store');
        Route::resource('articles', AuthorArticleController::class)->except(['show', 'destroy']);
        Route::get('/categories', [AuthorCategoryController::class, 'index'])->name('categories.index');
        Route::get('/comments', [AuthorCommentController::class, 'index'])->name('comments.index');
        Route::delete('/comments/{comment}', [AuthorCommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Editor — bisa tulis & edit artikel sendiri (seperti author), plus meninjau
    // dan menerbitkan/mengarsipkan draft dari author lain (lihat ArticlePolicy::publish/archive).
    Route::middleware(['role:editor'])->prefix('editor')->name('editor.')->group(function () {
        Route::get('/dashboard', [EditorDashboardController::class, 'index'])->name('dashboard');
        Route::post('articles/upload-image', [EditorArticleController::class, 'uploadImage'])->name('articles.upload-image');
        Route::post('article-categories', [EditorArticleController::class, 'storeCategory'])->name('article-categories.store');
        Route::patch('articles/{article}/publish', [EditorArticleController::class, 'publish'])->name('articles.publish');
        Route::patch('articles/{article}/archive', [EditorArticleController::class, 'archive'])->name('articles.archive');
        Route::post('articles/{article}/request-revision', [EditorArticleController::class, 'requestRevision'])->name('articles.request-revision');
        Route::patch('articles/{article}/mark-ready', [EditorArticleController::class, 'markReady'])->name('articles.mark-ready');
        Route::resource('articles', EditorArticleController::class)->except(['destroy']);
        Route::get('/categories', [EditorCategoryController::class, 'index'])->name('categories.index');
        Route::get('/comments', [EditorCommentController::class, 'index'])->name('comments.index');
        Route::delete('/comments/{comment}', [EditorCommentController::class, 'destroy'])->name('comments.destroy');
        Route::get('/writers', [EditorWriterController::class, 'index'])->name('writers.index');
        Route::get('/calendar', [EditorCalendarController::class, 'index'])->name('calendar.index');
    });

    // Support — akses lihat-saja untuk user/kursus/enrollment (bantu troubleshooting
    // pengguna), plus moderasi forum & analytics yang dipakai bareng admin (lihat
    // grup role:admin|support di atas). Tidak ada create/edit/delete di sini sama
    // sekali, sesuai permission yang di-seed (lihat RolesAndPermissionsSeeder).
    Route::middleware(['role:support'])->prefix('support')->name('support.')->group(function () {
        Route::get('/dashboard', [SupportDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [SupportUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [SupportUserController::class, 'show'])->name('users.show');
        Route::get('/courses', [SupportCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [SupportCourseController::class, 'show'])->name('courses.show');
        Route::get('/enrollments', [SupportEnrollmentController::class, 'index'])->name('enrollments.index');

        // Tiket — penanganan oleh staf support (lihat juga rute tiket bersama
        // di atas untuk sisi pelapor/pengguna biasa).
        Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
        Route::patch('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.status');
        Route::patch('/tickets/{ticket}/priority', [SupportTicketController::class, 'updatePriority'])->name('tickets.priority');
        Route::post('/ticket-categories', [SupportTicketController::class, 'storeCategory'])->name('ticket-categories.store');

        // Basis Pengetahuan — CRUD oleh staf support.
        Route::resource('knowledge-base', SupportKnowledgeBaseController::class)
            ->except(['show'])
            ->parameters(['knowledge-base' => 'knowledgeBaseArticle']);

        // Jadwal Support — kalender shift/maintenance/meeting/kegiatan.
        Route::get('/schedule', [SupportScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [SupportScheduleController::class, 'store'])->name('schedule.store');
        Route::delete('/schedule/{schedule}', [SupportScheduleController::class, 'destroy'])->name('schedule.destroy');

        // Laporan — ringkasan performa tiket.
        Route::get('/reports', [SupportReportController::class, 'index'])->name('reports.index');
    });

    // Halaman status untuk instruktur yang belum/tidak disetujui admin — sengaja di luar
    // middleware 'instructor.approved' supaya tidak memicu redirect loop.
    Route::middleware(['role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/pending-approval', function () {
            $status = auth()->user()->profile?->approval_status ?? 'approved';

            if ($status === 'approved') {
                return redirect()->route('instructor.dashboard');
            }

            return view('instructor.pending-approval', ['status' => $status]);
        })->name('pending-approval');
    });

    // Instructor Routes
    Route::middleware(['role:instructor', 'instructor.approved'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [InstructorAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/earnings', [InstructorEarningsController::class, 'index'])->name('earnings.index');
        Route::post('/payout-requests', [\App\Http\Controllers\Instructor\PayoutController::class, 'store'])->name('payout-requests.store');

        Route::get('/announcements', [\App\Http\Controllers\Instructor\NotificationController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [\App\Http\Controllers\Instructor\NotificationController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [\App\Http\Controllers\Instructor\NotificationController::class, 'store'])->name('announcements.store');

        Route::get('/certificates', [\App\Http\Controllers\Instructor\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/export', [\App\Http\Controllers\Instructor\CertificateController::class, 'export'])->name('certificates.export');

        Route::get('/coupons', [\App\Http\Controllers\Instructor\CouponController::class, 'index'])->name('coupons.index');
        Route::resource('courses', InstructorCourseController::class)->except(['show']);
        Route::post('courses/{course}/duplicate', [InstructorCourseController::class, 'duplicate'])->name('courses.duplicate');
        Route::get('courses/{course}/preview', [InstructorCourseController::class, 'preview'])->name('courses.preview');
        Route::resource('assignments', InstructorAssignmentController::class)->except(['show']);
        Route::get('/api/courses/{course}/lessons', [InstructorAssignmentController::class, 'lessonsForCourse'])->name('api.courses.lessons');

        Route::prefix('courses/{course}')->name('courses.')->group(function () {
            Route::resource('sections', InstructorSectionController::class)->except(['show']);
            Route::post('sections/update-order', [InstructorSectionController::class, 'updateOrder'])->name('sections.update-order');

            // Lesson Management (nested di dalam section)
            Route::prefix('sections/{section}')->name('sections.')->group(function () {
                Route::resource('lessons', InstructorLessonController::class)->except(['show']);
                Route::post('lessons/update-order', [InstructorLessonController::class, 'updateOrder'])->name('lessons.update-order');

                // Lesson Resources (materi tambahan/lampiran)
                Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
                    Route::resource('resources', LessonResourceController::class)->except(['show']);
                    Route::post('resources/update-order', [LessonResourceController::class, 'updateOrder'])->name('resources.update-order');
                });
            });

            // Quiz Management (nested di dalam course)
            Route::resource('quizzes', QuizController::class)->except(['show']);
            Route::get('quizzes/{quiz}/analytics', [QuizController::class, 'analytics'])->name('quizzes.analytics');
            Route::patch('quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])->name('quizzes.toggle-publish');
            Route::get('quizzes/{quiz}/grading', [QuizController::class, 'grading'])->name('quizzes.grading');
            Route::get('quizzes/{quiz}/attempts/{attempt}/grade', [QuizController::class, 'gradeAttempt'])->name('quizzes.attempts.grade');
            Route::post('quizzes/{quiz}/attempts/{attempt}/grade', [QuizController::class, 'storeGrade'])->name('quizzes.attempts.store-grade');
            Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
            Route::put('quizzes/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
            Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'deleteQuestion'])->name('quizzes.questions.destroy');
        });

        Route::get('/public-profile', [InstructorPublicProfileController::class, 'edit'])->name('public-profile.edit');
        Route::put('/public-profile', [InstructorPublicProfileController::class, 'update'])->name('public-profile.update');

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{user}/courses/{course}/progress', [StudentController::class, 'studentCourseProgress'])->name('students.course.progress');

        // Submission & Grading Management
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/assignment/{assignment}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');

        // Review & Feedback Management
        Route::get('/reviews', [InstructorReviewController::class, 'index'])->name('reviews.index');
        Route::put('/reviews/{review}', [InstructorReviewController::class, 'update'])->name('reviews.update');

        // Forum & Community Management
        Route::get('/forums', [InstructorForumController::class, 'index'])->name('forums.index');
        Route::prefix('courses/{course}/forums/{forum}')->name('forums.')->group(function () {
            Route::get('/', [InstructorForumController::class, 'showForum'])->name('show');
            Route::get('/threads/{thread}', [InstructorForumController::class, 'showThread'])->name('thread.show');
            Route::post('/threads/{thread}/posts', [InstructorForumController::class, 'storePost'])->name('thread.post.store');
            Route::post('/threads/{thread}/posts/{post}/solution', [InstructorForumController::class, 'markAsSolution'])->name('thread.post.solution');
            Route::post('/threads/{thread}/lock', [InstructorForumController::class, 'toggleLock'])->name('thread.lock');
            Route::post('/threads/{thread}/pin', [InstructorForumController::class, 'togglePin'])->name('thread.pin');
            Route::delete('/threads/{thread}/posts/{post}', [InstructorForumController::class, 'deletePost'])->name('thread.post.delete');
            Route::delete('/threads/{thread}', [InstructorForumController::class, 'destroyThread'])->name('thread.destroy');
        });
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/my-courses', [StudentDashboardController::class, 'myCourses'])->name('my-courses');

        // Courses
        Route::get('/courses/{course:slug}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Lessons
        Route::get('/courses/{course}/lessons/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');
        Route::post('/lessons/{lesson}/complete', [StudentLessonController::class, 'complete'])->name('lessons.complete');

        // Lesson Resources (download materi tambahan)
        Route::get('/courses/{course}/sections/{section}/lessons/{lesson}/resources/{resource}/download', [LessonResourceController::class, 'download'])->name('resources.download');

        // Certificate
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');

        // Regenerate certificate
        Route::post('/certificates/regenerate/{course:slug}', [CertificateController::class, 'regenerate'])->name('certificates.regenerate');
        Route::get('/certificates/check/{course:slug}', [CertificateController::class, 'checkStatus'])->name('certificates.check');

        // Assignment
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/submit', [AssignmentController::class, 'create'])->name('assignments.submit');
        Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/assignments/{assignment}/cancel', [AssignmentController::class, 'cancel'])->name('assignments.cancel');

        // Enroll
        Route::post('/courses/{slug}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');

        // Quiz
        Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
        Route::get('/quiz-attempts/{attempt}', [StudentQuizController::class, 'attempt'])->name('quizzes.attempt');
        Route::post('/quiz-attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quiz-attempts/{attempt}/result', [StudentQuizController::class, 'result'])->name('quizzes.result');

        // Review
        Route::get('/courses/{course:slug}/review/create', [StudentReviewController::class, 'create'])->name('reviews.create');
        Route::post('/courses/{course:slug}/review', [StudentReviewController::class, 'store'])->name('reviews.store');
        Route::get('/courses/{course:slug}/review/edit', [StudentReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/courses/{course:slug}/review', [StudentReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/courses/{course:slug}/review', [StudentReviewController::class, 'destroy'])->name('reviews.destroy');

        // Forum
        Route::get('/forums', [StudentForumController::class, 'index'])->name('forums.index');
        Route::get('/courses/{course}/forums/{forum}', [StudentForumController::class, 'showForum'])->name('forums.show');
        Route::get('/courses/{course}/forums/{forum}/create-thread', [StudentForumController::class, 'createThread'])->name('forums.thread.create');
        Route::post('/courses/{course}/forums/{forum}/threads', [StudentForumController::class, 'storeThread'])->name('forums.thread.store');
        Route::get('/courses/{course}/forums/{forum}/threads/{thread}', [StudentForumController::class, 'showThread'])->name('forums.thread.show');
        Route::post('/courses/{course}/forums/{forum}/threads/{thread}/posts', [StudentForumController::class, 'storePost'])->name('forums.thread.post.store');
        Route::post('/courses/{course}/forums/{forum}/threads/{thread}/posts/{post}/like', [StudentForumController::class, 'likePost'])->name('forums.thread.post.like');
        Route::post('/courses/{course}/forums/{forum}/threads/{thread}/posts/{post}/report', [StudentForumController::class, 'reportPost'])->name('forums.thread.post.report');
        Route::get('/courses/{course}/forums/{forum}/threads/{thread}/edit', [StudentForumController::class, 'editThread'])->name('forums.thread.edit');
        Route::put('/courses/{course}/forums/{forum}/threads/{thread}', [StudentForumController::class, 'updateThread'])->name('forums.thread.update');
        Route::delete('/courses/{course}/forums/{forum}/threads/{thread}/posts/{post}', [StudentForumController::class, 'deletePost'])->name('forums.thread.post.delete');

        /*
       |--------------------------------------------------------------------------
       | Student Event Routes (perlu login)
       |--------------------------------------------------------------------------
       */
        Route::get('/events', [StudentEventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [StudentEventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/register', [StudentEventController::class, 'register'])->name('events.register');
        Route::get('/my-events', [StudentEventController::class, 'myEvents'])->name('events.my');
        Route::delete('/events/registrations/{registration}', [StudentEventController::class, 'cancelRegistration'])->name('events.cancel');
        Route::post('/events/registrations/{registration}/payment', [StudentEventController::class, 'uploadPaymentProof'])->name('events.upload-payment');

        // Achievement
        Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
        Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');
        Route::get('/leaderboard', [AchievementController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/courses/{course}/leaderboard', [AchievementController::class, 'courseLeaderboard'])->name('courses.leaderboard');
        Route::get('/badges', [AchievementController::class, 'badges'])->name('badges');

        Route::post('/lessons/{lesson}/track-time', [StudentLessonController::class, 'trackTime'])->name('lessons.track');

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/{course}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    });



    // Koneksi Gmail API untuk pengiriman email sistem (admin only)
    Route::middleware(['role:admin'])->prefix('auth/google')->name('auth.google.')->group(function () {
        Route::get('/redirect', [GoogleMailController::class, 'redirect'])->name('redirect');
        Route::get('/callback', [GoogleMailController::class, 'callback'])->name('callback');
    });

    // Profile Routes (Semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifikasi (Semua role)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

Route::middleware(['auth', 'verified'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [UserSettingController::class, 'index'])->name('index');
    Route::post('/general', [UserSettingController::class, 'updateGeneral'])->name('general');
    Route::post('/notifications', [UserSettingController::class, 'updateNotifications'])->name('notifications');
    Route::post('/learning', [UserSettingController::class, 'updateLearning'])->name('learning');
    Route::post('/privacy', [UserSettingController::class, 'updatePrivacy'])->name('privacy');
    Route::post('/accessibility', [UserSettingController::class, 'updateAccessibility'])->name('accessibility');
    Route::post('/reset', [UserSettingController::class, 'resetToDefault'])->name('reset');
});

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['id', 'en'])) {
        $locale = 'id';
    }

    // Simpan ke session
    session(['locale' => $locale]);
    app()->setLocale($locale);

    // Simpan ke database jika user login
    if (Illuminate\Support\Facades\Auth::check()) {
        $settings = App\Models\UserSetting::firstOrCreate(['user_id' => Illuminate\Support\Facades\Auth::id()]);
        $settings->update(['language' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

require __DIR__ . '/auth.php';