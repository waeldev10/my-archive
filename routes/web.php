<?php

declare(strict_types=1);

use Modules\Auth\Livewire\ForgotPassword;
use Modules\Auth\Livewire\Login;
use Modules\Auth\Livewire\Register;
use Modules\Auth\Livewire\ResetPassword;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Web UI routes using Livewire full-page components.
|
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest routes
Route::middleware('guest')->group(function (): void {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');

    // Google OAuth
    Route::get('auth/google/redirect', [\Modules\Auth\Http\Controllers\Web\SocialiteController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [\Modules\Auth\Http\Controllers\Web\SocialiteController::class, 'callback'])->name('google.callback');
});

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::post('logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('settings', \App\Livewire\Settings\Profile::class)->name('settings');
});

// Email verification routes
Route::get('email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
