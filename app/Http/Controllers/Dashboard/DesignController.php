<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Services\DesignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DesignController extends Controller
{
    /**
     * Service Layer
     */
    protected DesignService $designService;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }

    // ═══════════════════════════════════════════════════════
    // 📊 DASHBOARD OPERATIONS - ADMIN ONLY
    // ═══════════════════════════════════════════════════════

    /**
     * عرض قائمة جميع التصاميم (للمشرفين)
     * GET /dashboard/designs
     * 
     * Query Parameters:
     * - search: البحث في design name, user name
     * - size_id: فلترة حسب المقاس
     * - min_price: الحد الأدنى للسعر
     * - max_price: الحد الأقصى للسعر
     * - design_option_id: فلترة حسب خيار التصميم (bonus)
     * - user_id: فلترة حسب المستخدم
     * - is_active: فلترة حسب الحالة (true/false)
     * - sort_by: الترتيب (created_at, updated_at, price, name)
     * - sort_direction: اتجاه الترتيب (asc, desc)
     * - per_page: عدد العناصر في الصفحة (default: 15)
     * 
     * Response Format:
     * {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Design Name",
     *       "description": "Design Description",
     *       "price": 150.00,
     *       "is_active": true,
     *       "user": {
     *         "id": 5,
     *         "name": "User Name",
     *         "email": "user@example.com"
     *       },
     *       "images": [...],
     *       "sizes": [...],
     *       "design_options": [...],
     *       "created_at": "2024-01-15T10:30:00.000000Z"
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // التحقق من صلاحية المشرف
        $this->authorize('viewAny', Design::class);

        // الحصول على جميع التصاميم مع الفلاتر
        $filters = $request->only([
            'search',
            'size_id',
            'min_price',
            'max_price',
            'design_option_id',
            'user_id',
            'is_active',
            'sort_by',
            'sort_direction',
            'per_page'
        ]);

        $designs = $this->designService->getAllDesigns($filters);

        // إرجاع البيانات مع معلومات المستخدمين
        return DesignResource::collection($designs);
    }

    /**
     * عرض تصميم واحد (للمشرف)
     * GET /dashboard/designs/{design}
     * 
     * @param Design $design
     * @return DesignResource
     */
    public function show(Design $design): DesignResource
    {
        $this->authorize('view', $design);
        
        $design = $this->designService->getDesignById($design->id);
        
        return new DesignResource($design);
    }

    /**
     * عرض إحصائيات التصاميم
     * GET /dashboard/designs/stats
     * 
     * Response Format:
     * {
     *   "total_designs": 150,
     *   "active_designs": 120,
     *   "total_users_with_designs": 45,
     *   "average_price": 175.50,
     *   "designs_by_user": [...]
     * }
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        // الحصول على الإحصائيات من Service
        $stats = $this->designService->getDesignStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * البحث المتقدم في التصاميم
     * POST /dashboard/designs/search
     * 
     * Body:
     * {
     *   "query": "design name or user name",
     *   "filters": {
     *     "size_id": 1,
     *     "min_price": 100,
     *     "max_price": 500,
     *     "design_option_id": 5,
     *     "user_id": 10,
     *     "is_active": true,
     *     "date_from": "2024-01-01",
     *     "date_to": "2024-12-31"
     *   }
     * }
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function advancedSearch(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
            'filters' => 'nullable|array',
            'filters.size_id' => 'nullable|integer|exists:sizes,id',
            'filters.min_price' => 'nullable|numeric|min:0',
            'filters.max_price' => 'nullable|numeric|min:0|gte:filters.min_price',
            'filters.design_option_id' => 'nullable|integer|exists:design_options,id',
            'filters.user_id' => 'nullable|integer|exists:users,id',
            'filters.is_active' => 'nullable|boolean',
            'filters.date_from' => 'nullable|date',
            'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
        ]);

        // دمج البحث والفلاتر
        $searchParams = array_merge(
            $request->input('filters', []),
            ['search' => $request->input('query')]
        );

        // إضافة فلترة حسب التاريخ إذا كانت موجودة
        if (!empty($request->input('filters.date_from'))) {
            $searchParams['date_from'] = $request->input('filters.date_from');
        }

        if (!empty($request->input('filters.date_to'))) {
            $searchParams['date_to'] = $request->input('filters.date_to');
        }

        $designs = $this->designService->getAllDesigns($searchParams);

        return DesignResource::collection($designs);
    }
}
