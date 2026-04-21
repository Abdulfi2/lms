<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\ProfileController;
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

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        // Route::resource('/courses', AdminCourseController::class);
        // Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
        // Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');
    });

    // Instructor Routes
    Route::middleware(['role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
        // Route::resource('/courses', InstructorCourseController::class);
        // Route::get('/assignments', [InstructorDashboardController::class, 'assignments'])->name('assignments');
        // Route::get('/students', [InstructorDashboardController::class, 'students'])->name('students');
        // Route::get('/earnings', [InstructorDashboardController::class, 'earnings'])->name('earnings');
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        // Route::get('/my-courses', [StudentCourseController::class, 'myCourses'])->name('my-courses');
        // Route::get('/certificates', [StudentDashboardController::class, 'certificates'])->name('certificates');
        // Route::get('/assignments', [StudentDashboardController::class, 'assignments'])->name('assignments');
        // Route::get('/achievements', [StudentDashboardController::class, 'achievements'])->name('achievements');
    });

    // Profile Routes (Semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';