<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignOptionRequest;
use App\Http\Requests\UpdateDesignOptionRequest;
use App\Http\Resources\DesignOptionResource;
use App\Models\DesignOption;
use App\Services\DesignOptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DesignOptionController extends Controller
{
    /**
     * Service Layer
     */
    protected DesignOptionService $designOptionService;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(DesignOptionService $designOptionService)
    {
        $this->designOptionService = $designOptionService;
    }

    // ═══════════════════════════════════════════════════════
    // 📊 DASHBOARD OPERATIONS - ADMIN ONLY
    // ═══════════════════════════════════════════════════════

    /**
     * عرض قائمة جميع خيارات التصميم (للمشرفين)
     * GET /dashboard/design-options
     * 
     * Query Parameters:
     * - search: البحث في الاسم
     * - type: فلترة حسب النوع (color, dome_type, fabric_type, sleeve_type)
     * - is_active: فلترة حسب الحالة (true/false)
     * - sort_by: الترتيب (created_at, updated_at, name, type)
     * - sort_direction: اتجاه الترتيب (asc, desc)
     * - per_page: عدد العناصر في الصفحة (default: 15)
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // التحقق من صلاحية المشرف
        $this->authorize('viewAny', DesignOption::class);

        // الحصول على جميع خيارات التصميم مع الفلاتر
        $filters = $request->only([
            'search',
            'type',
            'is_active',
            'sort_by',
            'sort_direction',
            'per_page'
        ]);

        $options = $this->designOptionService->getAllOptions($filters);

        return DesignOptionResource::collection($options);
    }

    /**
     * عرض خيار تصميم واحد (للمشرف)
     * GET /dashboard/design-options/{designOption}
     * 
     * @param DesignOption $designOption
     * @return DesignOptionResource
     */
    public function show(DesignOption $designOption): DesignOptionResource
    {
        $this->authorize('view', $designOption);
        
        $option = $this->designOptionService->getOptionById($designOption->id);
        
        return new DesignOptionResource($option);
    }

    /**
     * إنشاء خيار تصميم جديد (للمشرف فقط)
     * POST /dashboard/design-options
     * 
     * @param StoreDesignOptionRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(StoreDesignOptionRequest $request)
    {
        // التحقق من صلاحية المشرف
        $this->authorize('create', DesignOption::class);

        $option = $this->designOptionService->createOption($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Design option created successfully',
                'message_ar' => 'تم إنشاء خيار التصميم بنجاح',
                'data' => new DesignOptionResource($option),
            ], 201);
        }

        return redirect()
            ->route('dashboard.design-options.show', $option)
            ->with('success', 'تم إنشاء خيار التصميم بنجاح');
    }

    /**
     * تحديث خيار تصميم (للمشرف فقط)
     * PUT /dashboard/design-options/{designOption}
     * PATCH /dashboard/design-options/{designOption}
     * 
     * @param UpdateDesignOptionRequest $request
     * @param DesignOption $designOption
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateDesignOptionRequest $request, DesignOption $designOption)
    {
        // التحقق من صلاحية المشرف
        $this->authorize('update', $designOption);

        $option = $this->designOptionService->updateOption($designOption, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Design option updated successfully',
                'message_ar' => 'تم تحديث خيار التصميم بنجاح',
                'data' => new DesignOptionResource($option),
            ]);
        }

        return redirect()
            ->route('dashboard.design-options.show', $option)
            ->with('success', 'تم تحديث خيار التصميم بنجاح');
    }

    /**
     * حذف خيار تصميم (للمشرف فقط)
     * DELETE /dashboard/design-options/{designOption}
     * 
     * @param \Illuminate\Http\Request $request
     * @param DesignOption $designOption
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request, DesignOption $designOption)
    {
        // التحقق من صلاحية المشرف
        $this->authorize('delete', $designOption);

        $this->designOptionService->deleteOption($designOption);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Design option deleted successfully',
                'message_ar' => 'تم حذف خيار التصميم بنجاح',
            ]);
        }

        return redirect()
            ->route('dashboard.design-options.index')
            ->with('success', 'تم حذف خيار التصميم بنجاح');
    }

    /**
     * عرض إحصائيات خيارات التصميم
     * GET /dashboard/design-options/stats
     * 
     * Response Format:
     * {
     *   "total": 50,
     *   "active": 45,
     *   "by_type": {
     *     "color": {"label": "Color", "count": 20, "active_count": 18},
     *     "dome_type": {...},
     *     ...
     *   }
     * }
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        // الحصول على الإحصائيات من Service
        $stats = $this->designOptionService->getOptionStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * الحصول على أنواع الخيارات المتاحة
     * GET /dashboard/design-options/types
     * 
     * Response Format:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "value": "color",
     *       "label": "Color",
     *       "label_ar": "اللون"
     *     },
     *     ...
     *   ]
     * }
     * 
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        $types = $this->designOptionService->getOptionTypes();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    /**
     * الحصول على خيارات التصميم مجمعة حسب النوع
     * GET /dashboard/design-options/grouped
     * 
     * Query Parameters:
     * - active_only: عرض الخيارات النشطة فقط (default: false للمشرف)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function grouped(Request $request): JsonResponse
    {
        $activeOnly = $request->boolean('active_only', false);
        $grouped = $this->designOptionService->getOptionsGroupedByType($activeOnly);

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }
}
