<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\WalletRecharged;
use App\Notifications\WalletRecharged as WalletRechargedNotification;

class SendWalletRechargedNotification
{
    /**
     * Handle the event.
     */
    public function handle(WalletRecharged $event): void
    {
        $event->user->notify(new WalletRechargedNotification(
            $event->amount,
            $event->transaction->id
        ));
    }
}
