<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponService
{
    /**
     * التحقق من صحة الكوبون وإرجاعه
     * 
     * ملاحظة: هذه الدالة تستخدم lockForUpdate، لذا يجب استدعاؤها داخل transaction
     * 
     * @param string $code
     * @param float|null $amount المبلغ المطلوب التحقق منه
     * @param int|null $userId معرف المستخدم (للتحقق من استخدامه للكوبون من قبل)
     * @return Coupon|null
     * @throws \Exception
     */
    public function validateCoupon(string $code, ?float $amount = null, ?int $userId = null): ?Coupon
    {
        // استخدام lockForUpdate لمنع race condition عند استخدام نفس الكوبون في نفس الوقت
        // يجب استدعاء هذه الدالة داخل transaction
        $coupon = Coupon::byCode($code)->lockForUpdate()->first();

        if (!$coupon) {
            throw new \Exception('كود الكوبون غير صحيح');
        }

        // التحقق من حالة الكوبون
        if (!$coupon->is_active) {
            throw new \Exception('هذا الكوبون غير نشط');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new \Exception('هذا الكوبون منتهي الصلاحية');
        }

        if ($coupon->max_usage && $coupon->usage_count >= $coupon->max_usage) {
            throw new \Exception('تم الوصول إلى الحد الأقصى لاستخدام هذا الكوبون');
        }

        // التحقق من أن المستخدم لم يستخدم الكوبون من قبل
        if ($userId !== null && $coupon->hasBeenUsedByUser($userId)) {
            throw new \Exception('لقد استخدمت هذا الكوبون من قبل. يمكنك استخدام كل كوبون مرة واحدة فقط');
        }

        // التحقق من الحد الأدنى للشراء
        if ($amount !== null && $coupon->min_purchase && $amount < $coupon->min_purchase) {
            throw new \Exception('الحد الأدنى للشراء لاستخدام هذا الكوبون هو ' . number_format($coupon->min_purchase, 2) . ' ريال');
        }

        return $coupon;
    }

    /**
     * تطبيق الكوبون على مبلغ معين
     * 
     * @param string $code
     * @param float $amount
     * @param int|null $userId معرف المستخدم (للتحقق من استخدامه للكوبون من قبل)
     * @return array ['coupon' => Coupon, 'discount_amount' => float, 'final_amount' => float]
     * @throws \Exception
     */
    public function applyCoupon(string $code, float $amount, ?int $userId = null): array
    {
        $coupon = $this->validateCoupon($code, $amount, $userId);
        
        $discountAmount = $coupon->calculateDiscount($amount);
        
        // التأكد من أن مبلغ الخصم لا يتجاوز المبلغ الأصلي
        if ($discountAmount > $amount) {
            $discountAmount = $amount;
        }
        
        $finalAmount = $amount - $discountAmount;
        
        // التأكد من أن المبلغ النهائي ليس سالباً
        if ($finalAmount < 0) {
            $finalAmount = 0;
        }

        return [
            'coupon' => $coupon, 
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'subtotal' => $amount,
        ];
    }

    /**
     * تسجيل استخدام الكوبون في طلب
     * 
     * @param Coupon $coupon
     * @param int $userId معرف المستخدم
     * @param int|null $orderId معرف الطلب (اختياري)
     * @return void
     */
    public function recordUsage(Coupon $coupon, int $userId, ?int $orderId = null): void
    {
        // يتم استدعاء هذه الدالة عادة داخل transaction أكبر (مثل عند إنشاء الطلب)
        // لذا لا نحتاج transaction هنا لتجنب nested transactions
        
        // تسجيل استخدام المستخدم للكوبون
        $coupon->users()->attach($userId, [
            'order_id' => $orderId,
            'used_at' => now(),
        ]);
        
        // زيادة عدد مرات الاستخدام العامة
        $coupon->incrementUsage();
        
        Log::info('Coupon usage recorded', [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'user_id' => $userId,
            'order_id' => $orderId,
            'usage_count' => $coupon->fresh()->usage_count,
        ]);
    }

    /**
     * إنشاء كوبون جديد
     * 
     * @param array $data
     * @return Coupon
     */
    public function createCoupon(array $data): Coupon
    {
        return DB::transaction(function () use ($data) {
            $coupon = Coupon::create($data);
            
            Log::info('Coupon created', [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
            ]);

            return $coupon;
        });
    }

    /**
     * تحديث كوبون
     * 
     * @param Coupon $coupon
     * @param array $data
     * @return Coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $coupon->update($data);
            
            Log::info('Coupon updated', [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
            ]);

            return $coupon->fresh();
        });
    }

    /**
     * حذف كوبون
     * 
     * @param Coupon $coupon
     * @return bool
     */
    public function deleteCoupon(Coupon $coupon): bool
    {
        return DB::transaction(function () use ($coupon) {
            $code = $coupon->code;
            $deleted = $coupon->delete();
            
            if ($deleted) {
                Log::info('Coupon deleted', [
                    'coupon_id' => $coupon->id,
                    'coupon_code' => $code,
                ]);
            }

            return $deleted;
        });
    }

    /**
     * الحصول على إحصائيات الكوبونات
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $total = Coupon::count();
        $active = Coupon::active()->valid()->count();
        $expired = Coupon::where(function ($q) {
            $q->where('expires_at', '<', now())
              ->orWhere('is_active', false);
        })->count();
        $used = Coupon::where('usage_count', '>', 0)->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'used' => $used,
        ];
    }
}

