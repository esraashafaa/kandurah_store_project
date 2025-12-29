<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\DesignOption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignOptionPolicy
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
     * أي مستخدم يمكنه عرض قائمة خيارات التصميم (للقراءة فقط)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * أي مستخدم يمكنه عرض خيار التصميم
     */
    public function view(User $user, DesignOption $designOption): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * فقط الأدمن يمكنه إنشاء خيارات التصميم
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     * فقط الأدمن يمكنه تحديث خيارات التصميم
     */
    public function update(User $user, DesignOption $designOption): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     * فقط الأدمن يمكنه حذف خيارات التصميم
     */
    public function delete(User $user, DesignOption $designOption): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     * فقط الأدمن
     */
    public function restore(User $user, DesignOption $designOption): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     * فقط الأدمن
     */
    public function forceDelete(User $user, DesignOption $designOption): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }
}
