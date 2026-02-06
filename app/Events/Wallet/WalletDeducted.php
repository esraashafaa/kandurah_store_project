<?php

namespace App\Events\Wallet;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletDeducted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public float $amount,
        public Transaction $transaction,
        public ?string $description = null
    ) {
        //
    }
}
