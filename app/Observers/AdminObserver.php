<?php

namespace App\Observers;

use App\Models\Admin;
use App\Events\Admins\AdminCreated;
use App\Events\Admins\AdminRemoved;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

class AdminObserver
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Admin $admin): void
    {
        // الحصول على Admin من guard 'admin' أو من guard الافتراضي
        $createdBy = auth()->guard('admin')->user() ?? (auth()->user() instanceof Admin ? auth()->user() : null);
        
        Event::dispatch(new AdminCreated($admin, $createdBy));
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Admin $admin): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Admin $admin): void
    {
        // الحصول على Admin من guard 'admin' أو من guard الافتراضي
        $removedBy = auth()->guard('admin')->user() ?? (auth()->user() instanceof Admin ? auth()->user() : null);
        
        Event::dispatch(new AdminRemoved($admin, $removedBy, 'تم حذف الحساب'));
    }
}
