<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| REST API at /api/v1/* with Sanctum authentication.
| All endpoints are versioned under the v1 prefix.
|
*/

Route::prefix('api/v1')->group(function (): void {

    // ===== Public Auth Routes =====
    Route::prefix('auth')->group(function (): void {
        Route::post('register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
        Route::post('login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
        Route::post('google', [\App\Http\Controllers\Api\V1\AuthController::class, 'googleLogin']);
        Route::post('password/forgot', [\App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword']);
        Route::post('password/reset', [\App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword']);
    });

    // ===== Authenticated Routes =====
    Route::middleware('auth:sanctum')->group(function (): void {

        // Auth
        Route::prefix('auth')->group(function (): void {
            Route::post('logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
            Route::get('user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);
            Route::post('email/verify/resend', [\App\Http\Controllers\Api\V1\AuthController::class, 'resendVerification']);
        });

        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);

        // Archives — all 16 types
        Route::prefix('archives')->group(function (): void {
            Route::get('{type}', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'index']);
            Route::post('{type}', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'store']);
            Route::get('{type}/{archive}', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'show']);
            Route::put('{type}/{archive}', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'update']);
            Route::delete('{type}/{archive}', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'destroy']);
            Route::post('{type}/{archive}/restore', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'restore']);
            Route::delete('{type}/{archive}/force', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'forceDelete']);
            Route::post('{type}/{archive}/favorite', [\App\Http\Controllers\Api\V1\ArchiveController::class, 'toggleFavorite']);
        });

        // Tags
        Route::prefix('tags')->group(function (): void {
            Route::get('/', [\App\Http\Controllers\Api\V1\TagController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\V1\TagController::class, 'store']);
            Route::put('{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'update']);
            Route::delete('{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'destroy']);
            Route::get('{tag}/archives', [\App\Http\Controllers\Api\V1\TagController::class, 'archives']);
        });

        // Search
        Route::get('search', [\App\Http\Controllers\Api\V1\SearchController::class, 'search']);

        // Settings
        Route::prefix('settings')->group(function (): void {
            Route::get('/', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);
            Route::put('profile', [\App\Http\Controllers\Api\V1\SettingsController::class, 'updateProfile']);
            Route::put('preferences', [\App\Http\Controllers\Api\V1\SettingsController::class, 'updatePreferences']);
            Route::post('tokens', [\App\Http\Controllers\Api\V1\SettingsController::class, 'createToken']);
            Route::delete('tokens/{token}', [\App\Http\Controllers\Api\V1\SettingsController::class, 'revokeToken']);
        });

        // AI
        Route::prefix('ai')->group(function (): void {
            Route::post('classify', [\App\Http\Controllers\Api\V1\AiController::class, 'classify']);
            Route::post('tags', [\App\Http\Controllers\Api\V1\AiController::class, 'suggestTags']);
            Route::post('summarize', [\App\Http\Controllers\Api\V1\AiController::class, 'summarize']);
            Route::post('chat', [\App\Http\Controllers\Api\V1\AiController::class, 'chat']);
            Route::get('conversations', [\App\Http\Controllers\Api\V1\AiController::class, 'conversations']);
            Route::get('conversations/{conversation}', [\App\Http\Controllers\Api\V1\AiController::class, 'showConversation']);
            Route::delete('conversations/{conversation}', [\App\Http\Controllers\Api\V1\AiController::class, 'deleteConversation']);
        });

        // Telegram
        Route::prefix('telegram')->group(function (): void {
            Route::post('connect', [\App\Http\Controllers\Api\V1\TelegramController::class, 'connect']);
            Route::get('status', [\App\Http\Controllers\Api\V1\TelegramController::class, 'status']);
            Route::delete('disconnect', [\App\Http\Controllers\Api\V1\TelegramController::class, 'disconnect']);
        });

        // Admin
        Route::prefix('admin')->middleware('admin')->group(function (): void {
            Route::get('users', [\App\Http\Controllers\Api\V1\AdminController::class, 'listUsers']);
            Route::put('users/{user}/role', [\App\Http\Controllers\Api\V1\AdminController::class, 'updateUserRole']);
            Route::get('settings', [\App\Http\Controllers\Api\V1\AdminController::class, 'getSettings']);
            Route::put('settings', [\App\Http\Controllers\Api\V1\AdminController::class, 'updateSettings']);
        });
    });
});
