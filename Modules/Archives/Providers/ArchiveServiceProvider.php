<?php

declare(strict_types=1);

namespace Modules\Archives\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Archives\Models\Archive;
use Modules\Archives\Policies\ArchivePolicy;

class ArchiveServiceProvider extends ServiceProvider
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
        // Register web routes with the 'web' middleware group
        Route::middleware('web')
            ->group(__DIR__.'/../routes/web.php');

        // Register API routes with the 'api' middleware group, under /api/v1
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(__DIR__.'/../routes/api.php');

        // Register view namespace for module views
        $this->loadViewsFrom(__DIR__.'/../Views', 'archives');

        // Register authorization policies
        Gate::policy(Archive::class, ArchivePolicy::class);
    }
}
