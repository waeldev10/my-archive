<?php

declare(strict_types=1);

use Modules\Auth\Http\Controllers\Web\AuthController;
use Modules\Auth\Http\Controllers\Web\SocialiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Web Routes
|--------------------------------------------------------------------------
|
| Authentication web UI routes, following the Controller → Blade Page →
| Livewire Components entry pattern.
|
*/

// Guest routes
Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Google OAuth
    Route::get('auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [SocialiteController::class, 'callback'])->name('google.callback');
});

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Email verification routes
Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');
