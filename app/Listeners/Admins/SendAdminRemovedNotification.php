<?php

namespace App\Listeners\Admins;

use App\Events\Admins\AdminRemoved;
use App\Notifications\Admins\AdminRemovedNotification;
use App\Models\Admin;
use Illuminate\Support\Facades\Notification;

class SendAdminRemovedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AdminRemoved $event): void
    {
        // إرسال إشعار لجميع Super Admins
        $superAdmins = Admin::role('super-admin')->get();
        
        if ($superAdmins->isNotEmpty()) {
            Notification::send(
                $superAdmins,
                new AdminRemovedNotification($event->admin, $event->removedBy, $event->reason)
            );
        }
    }
}
