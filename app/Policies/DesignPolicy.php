<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Design;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignPolicy
{
    /**
     * قبل كل الفحوصات - الأدمن له صلاحية كاملة
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN])) {
            return true;
        }

        return null; // استمر في الفحص العادي
    }

    /**
     * Determine whether the user can view any models.
     * أي مستخدم مسجل يمكنه عرض قائمة التصاميم
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * أي مستخدم يمكنه عرض التصميم (للتصاميم النشطة)
     * أو صاحب التصميم (للتصاميم غير النشطة)
     */
    public function view(?User $user, Design $design): bool
    {
        // التصاميم النشطة يمكن لأي أحد مشاهدتها
        if ($design->is_active) {
            return true;
        }

        // التصاميم غير النشطة يمكن لصاحبها فقط مشاهدتها
        return $user && $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can create models.
     * أي مستخدم مسجل يمكنه إنشاء تصميم
     */
    public function create(User $user): bool
    {
        return $user->role === RoleEnum::USER;
    }

    /**
     * Determine whether the user can update the model.
     * فقط صاحب التصميم يمكنه التعديل
     */
    public function update(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * فقط صاحب التصميم يمكنه الحذف
     */
    public function delete(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * فقط صاحب التصميم أو الأدمن
     */
    public function restore(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * فقط الأدمن (تم التحقق في before())
     */
    public function forceDelete(User $user, Design $design): bool
    {
        return false; // سيتم السماح للأدمن في before()
    }
}
