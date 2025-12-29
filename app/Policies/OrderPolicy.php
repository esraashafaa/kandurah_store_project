<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Admin يقدر يشوف كل الطلبات
     * User يقدر يشوف طلباته فقط
     */
    public function viewAny(User $user): bool
    {
        return true; // الكل يقدر يشوف (بس راح نفلتر في الـ Query)
    }

    /**
     * عرض طلب معين
     */
    public function view(User $user, Order $order): bool
    {
        // Admin يقدر يشوف كل شي
        if ($user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN) {
            return true;
        }

        // User يقدر يشوف طلباته فقط
        return $order->user_id === $user->id;
    }

    /**
     * إنشاء طلب جديد
     */
    public function create(User $user): bool
    {
        // المستخدمين العاديين فقط يقدرون يطلبون
        // يمكن أيضاً للـ Admin إنشاء طلبات نيابة عن المستخدمين
        return $user->role === RoleEnum::USER 
            || $user->role === RoleEnum::ADMIN 
            || $user->role === RoleEnum::SUPER_ADMIN;
    }

    /**
     * تحديث الطلب
     */
    public function update(User $user, Order $order): bool
    {
        // Admin يقدر يحدث أي طلب (تغيير الحالة)
        if ($user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN) {
            return true;
        }

        // User يقدر يحدث طلباته (مثلاً تغيير الملاحظات)
        // لكن فقط إذا الطلب ما زال pending
        return $order->user_id === $user->id 
            && $order->status === \App\Enums\OrderStatus::PENDING;
    }

    /**
     * حذف الطلب (إلغاء)
     */
    public function delete(User $user, Order $order): bool
    {
        // Admin يقدر يحذف أي طلب
        if ($user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN) {
            return true;
        }

        // User يقدر يلغي طلباته فقط إذا pending أو confirmed
        return $order->user_id === $user->id && $order->canBeCancelled();
    }

    /**
     * تغيير حالة الطلب
     */
    public function updateStatus(User $user, Order $order): bool
    {
        // Admin فقط يقدر يغير الحالة
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }
}