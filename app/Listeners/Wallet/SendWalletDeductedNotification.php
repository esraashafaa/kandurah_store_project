<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\WalletDeducted;
use App\Notifications\Wallet\WalletDeductedNotification;

class SendWalletDeductedNotification
{
    /**
     * Handle the event.
     */
    public function handle(WalletDeducted $event): void
    {
        $event->user->notify(new WalletDeductedNotification(
            $event->amount,
            $event->transaction,
            $event->description
        ));
    }
}
