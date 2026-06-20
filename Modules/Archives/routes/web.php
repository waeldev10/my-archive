<?php

declare(strict_types=1);

use Modules\Archives\Http\Controllers\Web\ArchiveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Archives Web Routes
|--------------------------------------------------------------------------
|
| Web UI routes for archive CRUD, following the Controller → Blade Page →
| Livewire Components entry pattern.
|
*/

Route::middleware('auth')->prefix('archives')->group(function (): void {
    Route::get('{type}', [ArchiveController::class, 'index'])->name('archives.list');
    Route::get('{type}/create', [ArchiveController::class, 'create'])->name('archives.create');
    Route::get('{type}/{archive}', [ArchiveController::class, 'show'])->name('archives.show');
    Route::get('{type}/{archive}/edit', [ArchiveController::class, 'edit'])->name('archives.edit');
});
