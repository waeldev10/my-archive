<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Application-level web routes (welcome page).
| Module-specific routes are loaded by their service providers:
|   - Auth:     Modules/Auth/Providers/AuthServiceProvider
|   - Archives: Modules/Archives/Providers/ArchiveServiceProvider
|   - Dashboard: Modules/Dashboard/Providers/DashboardServiceProvider
|
*/

// Welcome page — controller entry point
Route::get('/', [PageController::class, 'welcome'])->name('home');
