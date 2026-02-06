<?php

namespace App\Listeners\Admins;

use App\Events\Admins\AdminPermissionsUpdated;
use App\Notifications\Admins\AdminPermissionsUpdatedNotification;
use App\Models\Admin;
use Illuminate\Support\Facades\Notification;

class SendAdminPermissionsUpdatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AdminPermissionsUpdated $event): void
    {
        // إرسال إشعار لجميع Super Admins
        $superAdmins = Admin::role('super-admin')->get();
        
        if ($superAdmins->isNotEmpty()) {
            Notification::send(
                $superAdmins,
                new AdminPermissionsUpdatedNotification(
                    $event->admin,
                    $event->oldPermissions,
                    $event->newPermissions,
                    $event->updatedBy
                )
            );
        }
    }
}
