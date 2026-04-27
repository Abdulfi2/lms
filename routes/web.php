<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FailedJobsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\SectionController as InstructorSectionController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\StudentController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PublicEventController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController as PublicCourseController;
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
Route::get('/', function () {
    return view('client.pages.home');
})->name('home');

Route::get('/certificate/verify/{code}', [CertificateController::class, 'verify'])->name('certificate.verify');

/*
|--------------------------------------------------------------------------
| Public Event Routes (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::post('/events/{slug}/register', [PublicEventController::class, 'register'])->name('events.register');

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect berdasarkan role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [PublicCourseController::class, 'show'])->name('courses.show');

    /*
    |--------------------------------------------------------------------------
    | Admin Event Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin|event_manager'])->prefix('admin')->name('admin.')->group(function () {

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

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        // Route::resource('/courses', AdminCourseController::class);
        // Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
        // Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');

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

        // Categories & Tags
        Route::resource('categories', CategoryController::class);
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::resource('tags', TagController::class);
        Route::patch('tags/{tag}/toggle-status', [TagController::class, 'toggleStatus'])->name('tags.toggle-status');

        // Course
        Route::resource('courses', CourseController::class);
        Route::patch('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');

        Route::prefix('courses/{course}')->group(function () {
            Route::resource('sections', SectionController::class)->except(['show']);
            Route::post('sections/update-order', [SectionController::class, 'updateOrder'])->name('courses.sections.update-order');

            Route::prefix('sections/{section}')->group(function () {
                Route::resource('lessons', LessonController::class)->except(['show']);
                Route::post('lessons/update-order', [LessonController::class, 'updateOrder'])->name('courses.sections.lessons.update-order');
            });
        });

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Failed Jobs Management
        Route::get('/failed-jobs', [FailedJobsController::class, 'index'])->name('failed-jobs.index');
        Route::get('/failed-jobs/{id}', [FailedJobsController::class, 'show'])->name('failed-jobs.show');
        Route::post('/failed-jobs/{id}/retry', [FailedJobsController::class, 'retry'])->name('failed-jobs.retry');
        Route::post('/failed-jobs/retry-all', [FailedJobsController::class, 'retryAll'])->name('failed-jobs.retry-all');
        Route::delete('/failed-jobs/{id}', [FailedJobsController::class, 'delete'])->name('failed-jobs.delete');
        Route::delete('/failed-jobs', [FailedJobsController::class, 'deleteAll'])->name('failed-jobs.delete-all');
    });

    // Instructor Routes
    Route::middleware(['role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
        Route::resource('courses', InstructorCourseController::class);

        Route::prefix('courses/{course}')->name('courses.')->group(function () {
            Route::resource('sections', InstructorSectionController::class);
            Route::post('sections/update-order', [InstructorSectionController::class, 'updateOrder'])->name('sections.update-order');

            // Lesson Management (nested di dalam section)
            Route::prefix('sections/{section}')->name('sections.')->group(function () {
                Route::resource('lessons', InstructorLessonController::class);
                Route::post('lessons/update-order', [InstructorLessonController::class, 'updateOrder'])->name('lessons.update-order');
            });

            Route::resource('quizzes', QuizController::class);
            Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
            Route::put('quizzes/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
            Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'deleteQuestion'])->name('quizzes.questions.destroy');
        });

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{user}/progress', [StudentController::class, 'progress'])->name('students.progress');
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

        // Certificates
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');

        // Assignments
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
        Route::get('/courses/{course}/review', [StudentReviewController::class, 'create'])->name('student.reviews.create');
        Route::post('/courses/{course}/review', [StudentReviewController::class, 'store'])->name('student.reviews.store');

        // Forum
        Route::get('/forums', [StudentForumController::class, 'index'])->name('forums.index');
        Route::get('/courses/{course}/forums/{forum}', [StudentForumController::class, 'showForum'])->name('forums.show');
        Route::get('/courses/{course}/forums/{forum}/create-thread', [StudentForumController::class, 'createThread'])->name('forums.thread.create');
        Route::post('/courses/{course}/forums/{forum}/threads', [StudentForumController::class, 'storeThread'])->name('forums.thread.store');
        Route::get('/courses/{course}/forums/{forum}/threads/{thread}', [StudentForumController::class, 'showThread'])->name('forums.thread.show');
        Route::post('/courses/{course}/forums/{forum}/threads/{thread}/posts', [StudentForumController::class, 'storePost'])->name('forums.thread.post.store');
        Route::post('/courses/{course}/forums/{forum}/threads/{thread}/posts/{post}/like', [StudentForumController::class, 'likePost'])->name('forums.thread.post.like');
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

        // Assignment routes
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/submit', [AssignmentController::class, 'create'])->name('assignments.submit');
        Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/assignments/{assignment}/cancel', [AssignmentController::class, 'cancel'])->name('assignments.cancel');

        // Achievement
        Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
        Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');
        Route::get('/leaderboard', [AchievementController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/badges', [AchievementController::class, 'badges'])->name('badges');
    });

    // Forum routes (bisa diakses student & instructor yang terdaftar di course)
    Route::middleware(['role:instructor|student'])->group(function () {
        Route::prefix('courses/{course}/forum')->name('forums.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/create-thread', [ForumController::class, 'createThread'])->name('thread.create');
            Route::post('/threads', [ForumController::class, 'storeThread'])->name('thread.store');
            Route::get('/threads/{thread}', [ForumController::class, 'showThread'])->name('thread.show');
            Route::post('/threads/{thread}/posts', [ForumController::class, 'storePost'])->name('thread.post.store');
            Route::post('/threads/{thread}/posts/{post}/like', [ForumController::class, 'likePost'])->name('thread.post.like');
            Route::post('/threads/{thread}/posts/{post}/solution', [ForumController::class, 'markAsSolution'])->name('thread.post.solution');
            Route::post('/threads/{thread}/lock', [ForumController::class, 'toggleLock'])->name('thread.lock');
            Route::post('/threads/{thread}/pin', [ForumController::class, 'togglePin'])->name('thread.pin');
            Route::delete('/threads/{thread}/posts/{post}', [ForumController::class, 'deletePost'])->name('thread.post.delete');
        });
    });


    // Profile Routes (Semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/google', [MailController::class, 'getAuthUrl']);
Route::get('/auth/google/callback', [MailController::class, 'handleCallback']);
Route::get('/send-email', [MailController::class, 'sendEmail']);

require __DIR__ . '/auth.php';