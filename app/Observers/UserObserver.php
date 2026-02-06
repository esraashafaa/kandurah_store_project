<?php

namespace App\Observers;

use App\Models\User;
use App\Events\Users\AccountActivated;
use App\Events\Users\AccountDeactivated;
use Illuminate\Support\Facades\Event;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // لا يوجد منطق خاص للمستخدمين العاديين
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // التحقق من تغيير حالة الحساب (is_active)
        if ($user->wasChanged('is_active')) {
            if ($user->is_active) {
                // تم تفعيل الحساب
                Event::dispatch(new AccountActivated($user));
            } else {
                // تم إلغاء تفعيل الحساب
                Event::dispatch(new AccountDeactivated($user));
            }
        }


        // التحقق من تغيير الصلاحيات (Spatie Permissions)
        // ملاحظة: هذا يتطلب مراقبة syncRoles, assignRole, removeRole
        // سنستخدم طريقة أخرى عبر Model Events أو في المكان الذي يتم فيه التغيير
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // لا يوجد منطق خاص للمستخدمين العاديين
    }
}
