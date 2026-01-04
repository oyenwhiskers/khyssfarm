<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\LogSuccessfulRegistration;
use App\Listeners\LogFailedLogin;

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
        // Use custom pagination views
        Paginator::defaultView('pagination::custom');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        // Define authorization gates
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('active', function ($user) {
            return $user->isActive();
        });

        // Register authentication event listeners
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
        Event::listen(Registered::class, LogSuccessfulRegistration::class);
        Event::listen(Failed::class, LogFailedLogin::class);
    }
}
