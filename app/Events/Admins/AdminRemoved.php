<?php

namespace App\Events\Admins;

use App\Models\Admin;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Admin $admin,
        public ?Admin $removedBy = null,
        public ?string $reason = null
    ) {
        //
    }
}
