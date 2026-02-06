<?php

namespace App\Providers;

use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderStatusChanged;
use App\Events\Designs\DesignCreated;
use App\Listeners\Orders\SendOrderCreatedNotification;
use App\Listeners\Orders\SendOrderStatusChangedNotification;
use App\Listeners\Designs\SendDesignCreatedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Order Events - فقط الإشعارات المطلوبة
        OrderCreated::class => [
            SendOrderCreatedNotification::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusChangedNotification::class,
            \App\Listeners\Orders\CreateInvoiceWhenOrderCompleted::class,
        ],
        // تم تعطيل OrderCancelled و OrderIssue

        // تم تعطيل جميع Wallet Events

        // تم تعطيل جميع Invoice Events

        // تم تعطيل جميع User Events

        // Design Events - فقط DesignCreated للأدمن
        DesignCreated::class => [
            SendDesignCreatedNotification::class,
        ],
        // تم تعطيل DesignUpdated

        // تم تعطيل جميع Admin Events
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
