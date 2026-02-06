<?php

namespace App\Events\Admins;

use App\Models\Admin;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminPermissionsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Admin $admin,
        public array $oldPermissions,
        public array $newPermissions,
        public ?Admin $updatedBy = null
    ) {
        //
    }
}
