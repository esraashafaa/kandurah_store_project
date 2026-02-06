<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * عرض محتويات السلة
     */
    public function index(): JsonResponse
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
        $count = $this->cartService->getCount();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'total_formatted' => number_format($total, 2) . ' SAR',
                'count' => $count,
            ],
        ]);
    }

    /**
     * إضافة تصميم للسلة
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'design_id' => 'required|integer|exists:designs,id',
            'quantity' => 'required|integer|min:1|max:100',
            'selected_options' => 'required|array',
            'selected_options.size_id' => 'required|integer|exists:sizes,id',
            'selected_options.color_ids' => 'nullable|array',
            'selected_options.color_ids.*' => 'integer|exists:design_options,id',
            'selected_options.fabric_id' => 'nullable|integer|exists:design_options,id',
            'selected_options.dome_type_id' => 'nullable|integer|exists:design_options,id',
            'selected_options.sleeve_type_id' => 'nullable|integer|exists:design_options,id',
        ]);

        $this->cartService->addItem(
            $validated['design_id'],
            $validated['selected_options'],
            $validated['quantity']
        );

        return response()->json([
            'success' => true,
            'message' => __('تم إضافة التصميم للسلة بنجاح'),
            'data' => [
                'cart_count' => $this->cartService->getCount(),
            ],
        ]);
    }

    /**
     * تحديث كمية عنصر في السلة
     */
    public function update(Request $request, string $cartKey): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $updated = $this->cartService->updateQuantity($cartKey, $validated['quantity']);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => __('العنصر غير موجود في السلة'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('تم تحديث الكمية بنجاح'),
            'data' => [
                'total' => $this->cartService->getTotal(),
            ],
        ]);
    }

    /**
     * حذف عنصر من السلة
     */
    public function destroy(string $cartKey): JsonResponse
    {
        $removed = $this->cartService->removeItem($cartKey);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => __('العنصر غير موجود في السلة'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('تم حذف العنصر من السلة'),
            'data' => [
                'cart_count' => $this->cartService->getCount(),
                'total' => $this->cartService->getTotal(),
            ],
        ]);
    }

    /**
     * تفريغ السلة بالكامل
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'message' => __('تم تفريغ السلة بنجاح'),
        ]);
    }
}