<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
        // التأكد من أن المستخدم admin
        $this->middleware('role:admin');
    }

    /**
     * عرض جميع الطلبات مع الفلترة والبحث
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getAllOrders(
            search: $request->input('search'),
            status: $request->input('status'),
            userId: $request->input('user_id'),
            minPrice: $request->input('min_price'),
            maxPrice: $request->input('max_price'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDir: $request->input('sort_dir', 'desc'),
            perPage: $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * إحصائيات الطلبات
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->orderService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * عرض تفاصيل طلب معين
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['items.design.images', 'location', 'user']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->updateStatus(
                $order,
                $request->input('status')
            );

            return response()->json([
                'success' => true,
                'message' => __('تم تحديث حالة الطلب بنجاح'),
                'data' => new OrderResource($order),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * حذف طلب (للأدمن فقط)
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => __('تم حذف الطلب بنجاح'),
        ]);
    }

    /**
     * تصدير الطلبات (CSV/Excel) - Bonus
     */
    public function export(Request $request): JsonResponse
    {
        // يمكنك استخدام Laravel Excel أو CSV Export
        // هذا مثال بسيط
        
        return response()->json([
            'success' => true,
            'message' => __('سيتم تنفيذ التصدير قريباً'),
        ]);
    }
}