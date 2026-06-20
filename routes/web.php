<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Application-level web routes (welcome page, dashboard).
| Module-specific routes are loaded by their service providers:
|   - Auth:     Modules/Auth/Providers/AuthServiceProvider
|   - Archives: Modules/Archives/Providers/ArchiveServiceProvider
|
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});
