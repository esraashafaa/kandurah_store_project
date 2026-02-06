<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    /**
     * Determine if the user can view any models.
     * فقط Super Admin يمكنه عرض قائمة المشرفين
     */
    public function viewAny($user): bool
    {
        if (!$user instanceof Admin) {
            return false;
        }
        
        // التحقق من أن المستخدم هو Super Admin
        return $user->hasRole('super-admin') || $user->role->value === 'super_admin';
    }

    /**
     * Determine if the user can view the model.
     * فقط Super Admin يمكنه عرض تفاصيل المشرف
     */
    public function view($user, Admin $admin): bool
    {
        if (!$user instanceof Admin) {
            return false;
        }
        
        // Super Admin يمكنه عرض أي مشرف
        if ($user->hasRole('super-admin') || $user->role->value === 'super_admin') {
            return true;
        }
        
        // المشرف يمكنه عرض حسابه الخاص فقط
        return $user->id === $admin->id;
    }

    /**
     * Determine if the user can create models.
     * فقط Super Admin يمكنه إنشاء مشرفين جدد
     */
    public function create($user): bool
    {
        if (!$user instanceof Admin) {
            return false;
        }
        
        return $user->hasRole('super-admin') || $user->role->value === 'super_admin';
    }

    /**
     * Determine if the user can update the model.
     * فقط Super Admin يمكنه تحديث المشرفين
     */
    public function update($user, Admin $admin): bool
    {
        if (!$user instanceof Admin) {
            return false;
        }
        
        // Super Admin يمكنه تحديث أي مشرف
        if ($user->hasRole('super-admin') || $user->role->value === 'super_admin') {
            return true;
        }
        
        // المشرف يمكنه تحديث حسابه الخاص فقط (لكن ليس الدور أو الصلاحيات)
        return $user->id === $admin->id;
    }

    /**
     * Determine if the user can delete the model.
     * فقط Super Admin يمكنه حذف المشرفين
     */
    public function delete($user, Admin $admin): bool
    {
        if (!$user instanceof Admin) {
            return false;
        }
        
        // لا يمكن حذف نفسك
        if ($user->id === $admin->id) {
            return false;
        }
        
        // فقط Super Admin يمكنه حذف المشرفين
        return $user->hasRole('super-admin') || $user->role->value === 'super_admin';
    }
}
