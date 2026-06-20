<?php

declare(strict_types=1);

use Modules\Archives\Livewire\ArchiveCreate;
use Modules\Archives\Livewire\ArchiveEdit;
use Modules\Archives\Livewire\ArchiveList;
use Modules\Archives\Livewire\ArchiveShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Archives Web Routes
|--------------------------------------------------------------------------
|
| Web UI routes for archive CRUD, using Livewire full-page components.
|
*/

Route::middleware('auth')->prefix('archives')->group(function (): void {
    Route::get('{type}', ArchiveList::class)->name('archives.list');
    Route::get('{type}/create', ArchiveCreate::class)->name('archives.create');
    Route::get('{type}/{archive}', ArchiveShow::class)->name('archives.show');
    Route::get('{type}/{archive}/edit', ArchiveEdit::class)->name('archives.edit');
});
