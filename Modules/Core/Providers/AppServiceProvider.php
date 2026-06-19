<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Register module view namespaces so that views can be referenced
        // as core::layouts.app, auth::auth.login, etc.
        View::addNamespace('core', __DIR__.'/../Views');
        View::addNamespace('auth', __DIR__.'/../../Auth/Views');
    }
}
