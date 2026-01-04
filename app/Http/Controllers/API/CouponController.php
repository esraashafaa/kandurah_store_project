<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {}

    /**
     * التحقق من صحة الكوبون
     * POST /api/coupons/validate
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $code = $request->input('code');
            $amount = $request->input('amount');
            $userId = $request->user()?->id;

            // استخدام validateCoupon بدون transaction للتحقق فقط
            // ولكن نحتاج transaction لاستخدام lockForUpdate
            // لذا سنستخدم طريقة بديلة للتحقق البسيط
            $coupon = \App\Models\Coupon::byCode($code)->first();

            if (!$coupon) {
                throw new \Exception('كود الكوبون غير صحيح');
            }

            // التحقق من صحة الكوبون
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
            
            // حساب مبلغ الخصم إذا تم إرسال المبلغ
            $discountAmount = null;
            $finalAmount = null;
            
            if ($amount !== null) {
                $discountAmount = $coupon->calculateDiscount($amount);
                $finalAmount = $amount - $discountAmount;
                
                // التأكد من أن مبلغ الخصم لا يتجاوز المبلغ الأصلي
                if ($discountAmount > $amount) {
                    $discountAmount = $amount;
                    $finalAmount = 0;
                }
                
                // التأكد من أن المبلغ النهائي ليس سالباً
                if ($finalAmount < 0) {
                    $finalAmount = 0;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'الكوبون صالح',
                'data' => [
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount' => $coupon->discount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'expires_at' => $coupon->expires_at?->format('Y-m-d'),
                    'description' => $coupon->description,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
