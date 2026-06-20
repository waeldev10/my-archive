<?php

declare(strict_types=1);

use Modules\Archives\Http\Controllers\Api\ArchiveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Archives API Routes
|--------------------------------------------------------------------------
|
| REST API endpoints for archive CRUD at /api/v1/archives/*.
|
*/

Route::middleware('auth:sanctum')->prefix('archives')->group(function (): void {
    Route::get('{type}', [ArchiveController::class, 'index']);
    Route::post('{type}', [ArchiveController::class, 'store']);
    Route::get('{type}/{archive}', [ArchiveController::class, 'show']);
    Route::put('{type}/{archive}', [ArchiveController::class, 'update']);
    Route::delete('{type}/{archive}', [ArchiveController::class, 'destroy']);
    Route::post('{type}/{archive}/restore', [ArchiveController::class, 'restore']);
    Route::delete('{type}/{archive}/force', [ArchiveController::class, 'forceDelete']);
    Route::post('{type}/{archive}/favorite', [ArchiveController::class, 'toggleFavorite']);
});
