<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Design;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignPolicy
{
    /**
     * قبل كل الفحوصات - الأدمن له صلاحية كاملة
     */
    public function before($user, string $ability): ?bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return null; // استمر في الفحص العادي
    }

    /**
     * Determine whether the user can view any models.
     * أي مستخدم مسجل يمكنه عرض قائمة التصاميم
     */
    public function viewAny($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * أي مستخدم يمكنه عرض التصميم (للتصاميم النشطة)
     * أو صاحب التصميم (للتصاميم غير النشطة)
     */
    public function view($user, Design $design): bool
    {
        // التصاميم النشطة يمكن لأي أحد مشاهدتها
        if ($design->is_active) {
            return true;
        }

        // التصاميم غير النشطة يمكن لصاحبها فقط مشاهدتها
        if ($user instanceof User) {
            return $user->id === $design->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * أي مستخدم مسجل يمكنه إنشاء تصميم
     */
    public function create($user): bool
    {
        return $user instanceof User;
    }

    /**
     * Determine whether the user can update the model.
     * فقط صاحب التصميم يمكنه التعديل
     */
    public function update($user, Design $design): bool
    {
        if ($user instanceof User) {
            return $user->id === $design->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * فقط صاحب التصميم يمكنه الحذف
     */
    public function delete($user, Design $design): bool
    {
        if ($user instanceof User) {
            return $user->id === $design->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     * فقط صاحب التصميم أو الأدمن
     */
    public function restore($user, Design $design): bool
    {
        if ($user instanceof User) {
            return $user->id === $design->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * فقط الأدمن (تم التحقق في before())
     */
    public function forceDelete($user, Design $design): bool
    {
        return false; // سيتم السماح للأدمن في before()
    }
}
