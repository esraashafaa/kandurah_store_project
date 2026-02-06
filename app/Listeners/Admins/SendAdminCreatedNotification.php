<?php

namespace App\Listeners\Admins;

use App\Events\Admins\AdminCreated;
use App\Notifications\Admins\AdminCreatedNotification;
use App\Models\Admin;
use Illuminate\Support\Facades\Notification;

class SendAdminCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AdminCreated $event): void
    {
        // إرسال إشعار لجميع Super Admins
        $superAdmins = Admin::role('super-admin')->get();
        
        if ($superAdmins->isNotEmpty()) {
            Notification::send(
                $superAdmins,
                new AdminCreatedNotification($event->admin, $event->createdBy)
            );
        }
    }
}
