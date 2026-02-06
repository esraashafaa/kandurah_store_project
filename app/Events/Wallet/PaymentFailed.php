<?php

namespace App\Events\Wallet;

use App\Models\User;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public Order $order,
        public string $reason,
        public string $paymentMethod // card, wallet, etc.
    ) {
        //
    }
}
