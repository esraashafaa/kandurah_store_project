<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderIssue;
use App\Notifications\Orders\OrderIssueNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendOrderIssueNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderIssue $event): void
    {
        // إرسال إشعار لجميع الأدمن
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new OrderIssueNotification(
                $event->order,
                $event->issueType,
                $event->message
            ));
        }
    }
}
