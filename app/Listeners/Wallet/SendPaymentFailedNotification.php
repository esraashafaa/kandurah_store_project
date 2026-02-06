<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\PaymentFailed;
use App\Notifications\Wallet\PaymentFailedNotification;
use App\Events\Orders\OrderIssue;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;

class SendPaymentFailedNotification
{
    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        // إرسال إشعار للمستخدم
        $event->user->notify(new PaymentFailedNotification(
            $event->order,
            $event->reason,
            $event->paymentMethod
        ));

        // إرسال إشعار للأدمن (OrderIssue)
        Event::dispatch(new OrderIssue(
            $event->order,
            'payment_failed',
            $event->reason
        ));
    }
}
