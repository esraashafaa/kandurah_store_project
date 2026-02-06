<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Admin يقدر يشوف كل الطلبات
     * User يقدر يشوف طلباته فقط
     */
    public function viewAny($user): bool
    {
        return true; // الكل يقدر يشوف (بس راح نفلتر في الـ Query)
    }

    /**
     * عرض طلب معين
     */
    public function view($user, Order $order): bool
    {
        // Admin يقدر يشوف كل شي
        if ($user instanceof Admin) {
            return true;
        }

        // User يقدر يشوف طلباته فقط
        if ($user instanceof User) {
            return $order->user_id === $user->id;
        }

        return false;
    }

    /**
     * إنشاء طلب جديد
     */
    public function create($user): bool
    {
        // المستخدمين العاديين والـ Admin يقدرون يطلبون
        return $user instanceof User || $user instanceof Admin;
    }

    /**
     * تحديث الطلب
     */
    public function update($user, Order $order): bool
    {
        // Admin يقدر يحدث أي طلب (تغيير الحالة)
        if ($user instanceof Admin) {
            return true;
        }

        // User يقدر يحدث طلباته (مثلاً تغيير الملاحظات)
        // لكن فقط إذا الطلب ما زال pending
        if ($user instanceof User) {
            return $order->user_id === $user->id 
                && $order->status === \App\Enums\OrderStatus::PENDING;
        }

        return false;
    }

    /**
     * حذف الطلب (إلغاء)
     */
    public function delete($user, Order $order): bool
    {
        // Admin يقدر يحذف أي طلب
        if ($user instanceof Admin) {
            return true;
        }

        // User يقدر يلغي طلباته فقط إذا pending أو confirmed
        if ($user instanceof User) {
            return $order->user_id === $user->id && $order->canBeCancelled();
        }

        return false;
    }

    /**
     * تغيير حالة الطلب
     */
    public function updateStatus($user, Order $order): bool
    {
        // Admin فقط يقدر يغير الحالة
        return $user instanceof Admin;
    }
}