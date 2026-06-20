<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Modules\Core\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
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
        // Register the core view namespace so views can be referenced as core::layouts.app
        View::addNamespace('core', __DIR__.'/../Views');

        // Register anonymous Blade component path for x-core::* components
        Blade::anonymousComponentPath(__DIR__.'/../Views/components', 'core');

        // Use custom Sanctum token model with ULID support
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
