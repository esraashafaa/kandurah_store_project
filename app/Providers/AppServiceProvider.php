<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Location;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\DesignObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use App\Policies\DesignPolicy;
use App\Policies\DesignOptionPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MeasurementPolicy;
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
        Measurement::class => MeasurementPolicy::class,
        Design::class => DesignPolicy::class,
        DesignOption::class => DesignOptionPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل FirebaseService كـ Singleton
        $this->app->singleton(\App\Services\FirebaseService::class, function ($app) {
            return new \App\Services\FirebaseService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تسجيل Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(Measurement::class, MeasurementPolicy::class);
        Gate::policy(Design::class, DesignPolicy::class);
        Gate::policy(DesignOption::class, DesignOptionPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Admin::class, \App\Policies\AdminPolicy::class);

        // تسجيل Observers
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        Design::observe(DesignObserver::class);
        Admin::observe(AdminObserver::class);
        
        // إضافة helper للحصول على المستخدم الحالي (User أو Admin)
        \Illuminate\Support\Facades\Auth::macro('admin', function () {
            // محاولة الحصول على Admin من guard 'admin'
            if (auth()->guard('admin')->check()) {
                return auth()->guard('admin')->user();
            }
            // محاولة الحصول من guard الافتراضي
            $user = auth()->user();
            if ($user instanceof Admin) {
                return $user;
            }
            return null;
        });
        
        // إضافة helper للحصول على المستخدم الحالي (User أو Admin) للاستخدام في views
        \Illuminate\Support\Facades\Auth::macro('currentUser', function () {
            // إذا كان هناك admin_id في session، نعيد Admin
            if (session('admin_id') && session('admin_guard') === 'admin') {
                $admin = \App\Models\Admin::find(session('admin_id'));
                if ($admin) {
                    return $admin;
                }
            }
            
            // محاولة الحصول على Admin من guard 'admin'
            if (auth()->guard('admin')->check()) {
                return auth()->guard('admin')->user();
            }
            
            // محاولة الحصول من guard الافتراضي
            $user = auth()->user();
            if ($user instanceof \App\Models\Admin) {
                return $user;
            }
            
            // إرجاع User العادي
            return $user;
        });
        
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
