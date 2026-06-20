<?php

declare(strict_types=1);

use Modules\Auth\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth API Routes
|--------------------------------------------------------------------------
|
| REST API endpoints for authentication at /api/v1/auth/*.
|
*/

// Public auth routes
Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');
    Route::post('login', [AuthController::class, 'login'])
        ->middleware(\Modules\Auth\Http\Middleware\LoginRateLimiter::class);
    Route::post('google', [AuthController::class, 'googleLogin']);
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
});

// Authenticated auth routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('email/verify/resend', [AuthController::class, 'resendVerification']);
});
