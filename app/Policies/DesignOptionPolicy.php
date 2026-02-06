<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\DesignOption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignOptionPolicy
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
     * أي مستخدم يمكنه عرض قائمة خيارات التصميم (للقراءة فقط)
     */
    public function viewAny($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * أي مستخدم يمكنه عرض خيار التصميم
     */
    public function view($user, DesignOption $designOption): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * فقط الأدمن يمكنه إنشاء خيارات التصميم
     */
    public function create($user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can update the model.
     * فقط الأدمن يمكنه تحديث خيارات التصميم
     */
    public function update($user, DesignOption $designOption): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can delete the model.
     * فقط الأدمن يمكنه حذف خيارات التصميم
     */
    public function delete($user, DesignOption $designOption): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can restore the model.
     * فقط الأدمن
     */
    public function restore($user, DesignOption $designOption): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * فقط الأدمن
     */
    public function forceDelete($user, DesignOption $designOption): bool
    {
        return $user instanceof Admin;
    }
}
