<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderCancelled;
use App\Notifications\Orders\OrderCancelledNotification;
use App\Notifications\Orders\NewOrderNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendOrderCancelledNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        // إرسال إشعار للمستخدم (صاحب الطلب)
        $order->user->notify(new OrderCancelledNotification($order, $event->reason));

        // إرسال إشعار لجميع الأدمن
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new OrderCancelledNotification($order, $event->reason));
        }
    }
}
