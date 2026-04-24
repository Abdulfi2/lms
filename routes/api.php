<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TokenController;
use Illuminate\Support\Facades\Route;

// Public API routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Token Management
    Route::get('/tokens', [TokenController::class, 'index']);
    Route::post('/tokens', [TokenController::class, 'store']);
    Route::delete('/tokens/{token}', [TokenController::class, 'destroy']);
    Route::delete('/tokens', [TokenController::class, 'destroyAll']);

    Route::get('categories', [CategoryController::class, 'apiList']);
    Route::get('tags', [TagController::class, 'apiList']);
});