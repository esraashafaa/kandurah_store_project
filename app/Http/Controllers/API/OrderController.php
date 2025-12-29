<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService
    ) {}

    /**
     * عرض طلبات المستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getUserOrders(
            userId: $request->user()->id,
            search: $request->input('search'),
            status: $request->input('status'),
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
     * إنشاء طلب جديد من السلة أو من items مباشرة
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $locationId = $request->input('location_id');
            $notes = $request->input('notes');

            // إذا تم إرسال items مباشرة، استخدم createOrderFromItems
            if ($request->has('items') && !empty($request->input('items'))) {
                $this->orderService->createOrderFromItems(
                    userId: $userId,
                    locationId: $locationId,
                    items: $request->input('items'),
                    notes: $notes
                );

                return response()->json([
                    'success' => true,
                    'message' => __('تم إنشاء الطلب بنجاح'),
                ], 201);
            }

            // وإلا استخدم السلة
            $cartItems = $this->cartService->getItems();

            if (empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => __('السلة فارغة. يرجى إضافة items مباشرة أو إضافة منتجات للسلة'),
                ], 422);
            }

            // إنشاء الطلب من السلة
            $this->orderService->createOrderFromCart(
                userId: $userId,
                locationId: $locationId,
                cartItems: $cartItems,
                notes: $notes
            );

            // تفريغ السلة
            $this->cartService->clear();

            return response()->json([
                'success' => true,
                'message' => __('تم إنشاء الطلب بنجاح'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('حدث خطأ أثناء إنشاء الطلب'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض تفاصيل طلب معين
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['items.design.images', 'location', 'user']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * إلغاء طلب
     */
    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        try {
            $order = $this->orderService->cancelOrder($order);

            return response()->json([
                'success' => true,
                'message' => __('تم إلغاء الطلب بنجاح'),
                'data' => new OrderResource($order),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}