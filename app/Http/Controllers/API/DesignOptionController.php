<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignOptionRequest;
use App\Http\Requests\UpdateDesignOptionRequest;
use App\Http\Resources\DesignOptionResource;
use App\Models\DesignOption;
use App\Services\DesignOptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller لخيارات التصميم
 * الأدمن فقط يمكنه الإنشاء والتعديل والحذف
 * المستخدمون يمكنهم العرض فقط
 */
class DesignOptionController extends Controller
{
    public function __construct(
        private DesignOptionService $designOptionService
    ) {}

    /**
     * عرض جميع خيارات التصميم
     * 
     * GET /api/design-options
     * Query params: type, is_active, search, per_page
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $filters = $request->only(['type', 'is_active', 'search', 'per_page', 'sort_by', 'sort_direction']);

        // للمستخدمين العاديين: عرض الخيارات النشطة فقط
        if (!$this->isAdmin($request)) {
            $filters['is_active'] = true;
        }

        $options = $this->designOptionService->getAllOptions($filters);

        return DesignOptionResource::collection($options);
    }

    /**
     * عرض خيارات التصميم مجمعة حسب النوع
     * 
     * GET /api/design-options/grouped
     */
    public function grouped(Request $request): JsonResponse
    {
        $activeOnly = !$this->isAdmin($request);
        $grouped = $this->designOptionService->getOptionsGroupedByType($activeOnly);

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    /**
     * عرض أنواع الخيارات المتاحة
     * 
     * GET /api/design-options/types
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
     * عرض خيار تصميم واحد
     * 
     * GET /api/design-options/{designOption}
     */
    public function show(DesignOption $designOption): DesignOptionResource
    {
        return new DesignOptionResource($designOption);
    }

    /**
     * إنشاء خيار تصميم جديد (للأدمن فقط)
     * 
     * POST /api/design-options
     */
    public function store(StoreDesignOptionRequest $request): JsonResponse
    {
        // التحقق من صلاحيات الأدمن
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $option = $this->designOptionService->createOption($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Design option created successfully',
            'message_ar' => 'تم إنشاء خيار التصميم بنجاح',
            'data' => new DesignOptionResource($option),
        ], 201);
    }

    /**
     * تحديث خيار تصميم (للأدمن فقط)
     * 
     * PUT /api/design-options/{designOption}
     */
    public function update(UpdateDesignOptionRequest $request, DesignOption $designOption): JsonResponse
    {
        // التحقق من صلاحيات الأدمن
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $option = $this->designOptionService->updateOption($designOption, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Design option updated successfully',
            'message_ar' => 'تم تحديث خيار التصميم بنجاح',
            'data' => new DesignOptionResource($option),
        ]);
    }

    /**
     * حذف خيار تصميم (للأدمن فقط)
     * 
     * DELETE /api/design-options/{designOption}
     */
    public function destroy(Request $request, DesignOption $designOption): JsonResponse
    {
        // التحقق من صلاحيات الأدمن
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $this->designOptionService->deleteOption($designOption);

        return response()->json([
            'success' => true,
            'message' => 'Design option deleted successfully',
            'message_ar' => 'تم حذف خيار التصميم بنجاح',
        ]);
    }

    /**
     * إحصائيات خيارات التصميم (للأدمن فقط)
     * 
     * GET /api/design-options/stats
     */
    public function stats(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $stats = $this->designOptionService->getOptionStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * التحقق من أن المستخدم أدمن
     */
    private function isAdmin(Request $request): bool
    {
        $user = $request->user();
        return $user instanceof \App\Models\Admin;
    }

    /**
     * رد خطأ صلاحيات
     */
    private function forbiddenResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to perform this action',
            'message_ar' => 'ليس لديك صلاحية لتنفيذ هذا الإجراء',
        ], 403);
    }
}
