<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\FailedJobsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\SectionController as InstructorSectionController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\StudentController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Student\AssignmentController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ReviewController as StudentReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController as PublicCourseController;
use App\Http\Controllers\Student\QuizAttemptController;
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
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments');

        // Enroll
        Route::post('/courses/{slug}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');

        Route::get('/quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
        Route::get('/quiz-attempts/{attempt}', [StudentQuizController::class, 'attempt'])->name('quizzes.attempt');
        Route::post('/quiz-attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quiz-attempts/{attempt}/result', [StudentQuizController::class, 'result'])->name('quizzes.result');

        // Review
        Route::get('/courses/{course}/review', [StudentReviewController::class, 'create'])->name('student.reviews.create');
        Route::post('/courses/{course}/review', [StudentReviewController::class, 'store'])->name('student.reviews.store');

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