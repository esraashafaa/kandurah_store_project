<?php

namespace App\Listeners\Users;

use App\Events\Users\AccountActivated;
use App\Notifications\Users\AccountActivatedNotification;

class SendAccountActivatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AccountActivated $event): void
    {
        $event->user->notify(new AccountActivatedNotification());
    }
}
