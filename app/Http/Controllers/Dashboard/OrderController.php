<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Service Layer
     */
    protected OrderService $orderService;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // ═══════════════════════════════════════════════════════
    // 📊 DASHBOARD OPERATIONS - ADMIN ONLY
    // ═══════════════════════════════════════════════════════

    /**
     * عرض صفحة إنشاء طلب جديد
     * GET /dashboard/orders/create
     * 
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Order::class);
        
        // الحصول على البيانات المطلوبة
        $users = \App\Models\User::where('is_active', true)->select('id', 'name', 'email')->get();
        $designs = \App\Models\Design::where('is_active', true)
            ->with(['images', 'sizes', 'designOptions'])
            ->get();
        $sizes = \App\Models\Size::where('is_active', true)->ordered()->get();
        $designOptions = \App\Models\DesignOption::where('is_active', true)
            ->get()
            ->groupBy('type');
        
        // Group design options by type for better display
        $groupedOptions = [];
        foreach ($designOptions as $type => $options) {
            $groupedOptions[$type] = $options;
        }
        
        return view('admin.orders.create', compact('users', 'designs', 'sizes', 'designOptions'));
    }

    /**
     * حفظ طلب جديد
     * POST /dashboard/orders
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Order::class);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'items' => 'required|array|min:1',
            'items.*.design_id' => 'required|exists:designs,id',
            'items.*.size_id' => 'required|exists:sizes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.design_option_ids' => 'nullable|array',
            'items.*.design_option_ids.*' => 'exists:design_options,id',
            'notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        try {
            $order = $this->orderService->createOrderFromItems(
                userId: $validated['user_id'],
                locationId: $validated['location_id'],
                items: $validated['items'],
                notes: $validated['notes'] ?? null,
                couponCode: $validated['coupon_code'] ?? null
            );

            return redirect()
                ->route('dashboard.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * عرض قائمة جميع الطلبات (للمشرفين)
     * GET /dashboard/orders
     * 
     * Query Parameters:
     * - search: البحث في order id, user name, user email
     * - status: فلترة حسب الحالة
     * - user_id: فلترة حسب المستخدم
     * - min_price: الحد الأدنى للمبلغ
     * - max_price: الحد الأقصى للمبلغ
     * - start_date: تاريخ البداية
     * - end_date: تاريخ النهاية
     * - sort_by: الترتيب (id, created_at, total_amount, status)
     * - sort_dir: اتجاه الترتيب (asc, desc)
     * - per_page: عدد العناصر في الصفحة (default: 15)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // التحقق من صلاحية المشرف
        $this->authorize('viewAny', Order::class);

        // جمع الفلاتر من الطلب
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'user_id' => $request->input('user_id'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
            'per_page' => $request->input('per_page', 15),
        ];

        // الحصول على الطلبات مع pagination
        $orders = $this->orderService->getAllOrders(
            search: $filters['search'],
            status: $filters['status'],
            userId: $filters['user_id'],
            minPrice: $filters['min_price'],
            maxPrice: $filters['max_price'],
            startDate: $filters['start_date'],
            endDate: $filters['end_date'],
            sortBy: $filters['sort_by'],
            sortDir: $filters['sort_dir'],
            perPage: $filters['per_page']
        );
        
        // الحصول على الإحصائيات
        $stats = $this->orderService->getStatistics();
        
        // الحصول على قائمة المستخدمين للفلترة
        $users = \App\Models\User::whereHas('orders')->select('id', 'name', 'email')->get();

        return view('admin.orders.index', compact('orders', 'stats', 'users'));
    }

    /**
     * عرض تفاصيل طلب معين (للمشرف)
     * GET /dashboard/orders/{order}
     * 
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order): \Illuminate\View\View
    {
        $this->authorize('view', $order);
        
        $order->load([
            'user',
            'location',
            'items.design.images',
            'items.design.user',
            'items.size'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * تحديث حالة الطلب (AJAX)
     * PUT /dashboard/orders/{order}/status
     * 
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|string|in:' . implode(',', \App\Enums\OrderStatus::values()),
        ]);

        try {
            $order = $this->orderService->updateStatus($order, $request->input('status'));

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح',
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
     * إلغاء الطلب (AJAX)
     * POST /dashboard/orders/{order}/cancel
     * 
     * @param Order $order
     * @return JsonResponse
     */
    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        try {
            $order = $this->orderService->cancelOrder($order);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب بنجاح',
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
     * إحصائيات الطلبات
     * GET /dashboard/orders/stats
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = $this->orderService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
