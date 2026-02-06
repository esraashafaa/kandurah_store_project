<?php

namespace App\Listeners\Designs;

use App\Events\Designs\DesignUpdated;
use App\Notifications\Designs\DesignUpdatedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendDesignUpdatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(DesignUpdated $event): void
    {
        // إرسال إشعار لجميع الأدمن
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new DesignUpdatedNotification($event->design));
        }
    }
}
