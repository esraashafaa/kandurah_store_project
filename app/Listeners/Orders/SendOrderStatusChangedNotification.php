<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderStatusChanged;
use App\Notifications\Orders\OrderStatusChangedNotification;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class SendOrderStatusChangedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;

        Log::info('SendOrderStatusChangedNotification received', [
            'order_id' => $order->id,
            'new_status' => $newStatus->value,
            'user_id' => $order->user_id,
        ]);

        // إرسال إشعار للمستخدم فقط عند تغييرات معينة
        if (in_array($newStatus, [
            OrderStatus::CONFIRMED,
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED,
        ])) {
            Log::info('Sending OrderStatusChangedNotification', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'new_status' => $newStatus->value,
            ]);

            $order->user->notify(
                new OrderStatusChangedNotification($order, $event->oldStatus, $newStatus)
            );

            Log::info('OrderStatusChangedNotification sent successfully', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } else {
            Log::info('OrderStatusChangedNotification skipped - status not in notification list', [
                'order_id' => $order->id,
                'new_status' => $newStatus->value,
            ]);
        }
    }
}
