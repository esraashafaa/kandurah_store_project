<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {}

    /**
     * عرض قائمة الكوبونات
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(12);
        $stats = $this->couponService->getStatistics();

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * عرض نموذج إنشاء كوبون جديد
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * حفظ كوبون جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount' => 'required|numeric|min:0|max:100',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
            'max_usage' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ], [
            'code.required' => 'كود الكوبون مطلوب',
            'code.unique' => 'هذا الكود مستخدم بالفعل',
            'discount.required' => 'نسبة الخصم مطلوبة',
            'discount.min' => 'نسبة الخصم يجب أن تكون أكبر من أو تساوي 0',
            'discount.max' => 'نسبة الخصم يجب أن تكون أقل من أو تساوي 100',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $coupon = $this->couponService->createCoupon($validated);
            
            return redirect()
                ->route('admin.coupons.index')
                ->with('success', 'تم إنشاء الكوبون بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل كوبون
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('orders.user');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * عرض نموذج تعديل كوبون
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * تحديث كوبون
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
            'max_usage' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ], [
            'code.required' => 'كود الكوبون مطلوب',
            'code.unique' => 'هذا الكود مستخدم بالفعل',
            'discount.required' => 'نسبة الخصم مطلوبة',
            'discount.min' => 'نسبة الخصم يجب أن تكون أكبر من أو تساوي 0',
            'discount.max' => 'نسبة الخصم يجب أن تكون أقل من أو تساوي 100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $this->couponService->updateCoupon($coupon, $validated);
            
            return redirect()
                ->route('admin.coupons.index')
                ->with('success', 'تم تحديث الكوبون بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حذف كوبون
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $this->couponService->deleteCoupon($coupon);
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الكوبون بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تفعيل/تعطيل كوبون
     */
    public function toggle(Coupon $coupon)
    {
        try {
            $coupon->update(['is_active' => !$coupon->is_active]);
            
            return response()->json([
                'success' => true,
                'message' => $coupon->is_active ? 'تم تفعيل الكوبون' : 'تم تعطيل الكوبون',
                'is_active' => $coupon->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
