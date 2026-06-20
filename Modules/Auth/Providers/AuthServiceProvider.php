<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
        // Register event listeners for auth-related events.
        // This ensures verification emails are sent on registration.
        Event::listen(
            Registered::class,
            SendEmailVerificationNotification::class,
        );

        // Register web routes with the 'web' middleware group
        Route::middleware('web')
            ->group(__DIR__.'/../routes/web.php');

        // Register API routes with the 'api' middleware group, under /api/v1
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(__DIR__.'/../routes/api.php');

        // Register view namespace for module views
        $this->loadViewsFrom(__DIR__.'/../Views', 'auth');
    }
}
