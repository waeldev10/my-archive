<?php

declare(strict_types=1);

namespace Modules\Dashboard\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Dashboard\Livewire\Dashboard;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire components
        Livewire::component('dashboard', Dashboard::class);

        // Register view namespace
        $this->loadViewsFrom(__DIR__.'/../Views', 'dashboard');

        // Register web routes
        Route::middleware('web')
            ->group(__DIR__.'/../routes/web.php');
    }
}
