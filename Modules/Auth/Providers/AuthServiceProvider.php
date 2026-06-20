<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Auth\Livewire\ForgotPassword;
use Modules\Auth\Livewire\Login;
use Modules\Auth\Livewire\Register;
use Modules\Auth\Livewire\ResetPassword;

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
        // Register Livewire components
        Livewire::component('auth.login', Login::class);
        Livewire::component('auth.register', Register::class);
        Livewire::component('auth.forgot-password', ForgotPassword::class);
        Livewire::component('auth.reset-password', ResetPassword::class);

        // Register event listeners for auth-related events
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
