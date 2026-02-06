<?php

namespace App\Listeners\Users;

use App\Events\Users\AccountDeactivated;
use App\Notifications\Users\AccountDeactivatedNotification;

class SendAccountDeactivatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AccountDeactivated $event): void
    {
        $event->user->notify(new AccountDeactivatedNotification($event->reason));
    }
}
