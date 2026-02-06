<?php

namespace App\Listeners\Users;

use App\Events\Users\AccountDeactivated;
use App\Notifications\Users\UserDeactivatedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendUserDeactivatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AccountDeactivated $event): void
    {
        // إرسال إشعار لجميع الأدمن
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new UserDeactivatedNotification(
                $event->user,
                $event->reason
            ));
        }
    }
}
