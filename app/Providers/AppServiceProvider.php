<?php

namespace App\Providers;

use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Policies\DesignPolicy;
use App\Policies\DesignOptionPolicy;
use App\Policies\LocationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Location::class => LocationPolicy::class,
        Design::class => DesignPolicy::class,
        DesignOption::class => DesignOptionPolicy::class,
        Order::class => OrderPolicy::class,
    ];

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
        // تسجيل Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(Design::class, DesignPolicy::class);
        Gate::policy(DesignOption::class, DesignOptionPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        
        // Set error handler to catch preg_replace null byte errors
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            if (strpos($errstr, 'preg_replace') !== false && strpos($errstr, 'Null byte') !== false) {
                // Log the error but don't display it
                \Log::warning("preg_replace null byte error prevented", [
                    'file' => $errfile,
                    'line' => $errline,
                    'error' => $errstr
                ]);
                return true; // Suppress the error
            }
            return false; // Let other errors through
        }, E_WARNING);
    }
}
