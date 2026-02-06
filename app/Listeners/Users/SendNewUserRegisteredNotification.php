<?php

namespace App\Listeners\Users;

use App\Events\Users\UserRegistered;
use App\Notifications\Users\NewUserRegisteredNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendNewUserRegisteredNotification
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // إرسال إشعار لجميع الأدمن
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($event->user));
        }
    }
}
