<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignRequest;
use App\Http\Requests\UpdateDesignRequest;
use App\Http\Resources\DesignCollection;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Services\DesignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller للتصاميم
 * 
 * المستخدم العادي:
 * - إنشاء تصميم جديد
 * - عرض تصاميمه الخاصة
 * - تعديل تصاميمه الخاصة
 * - حذف تصاميمه الخاصة
 * - تصفح تصاميم الآخرين (النشطة فقط)
 * 
 * الأدمن:
 * - عرض جميع التصاميم
 * - البحث والفلترة المتقدمة
 */
class DesignController extends Controller
{
    public function __construct(
        private DesignService $designService
    ) {}

    /**
     * عرض قائمة التصاميم
     * للأدمن: جميع التصاميم مع البحث والفلترة
     * للمستخدم: تصاميمه الخاصة فقط
     * 
     * GET /api/designs
     */
    public function index(Request $request): DesignCollection|JsonResponse
    {
        $user = $request->user();
        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'user_id', 'is_active',
            'sort_by', 'sort_direction', 'per_page'
        ]);

        // الأدمن يرى جميع التصاميم
        if ($this->isAdmin($user)) {
            $designs = $this->designService->getAllDesigns($filters);
        } else {
            // المستخدم يرى تصاميمه فقط
            $designs = $this->designService->getMyDesigns($user->id, $filters);
        }

        return new DesignCollection($designs);
    }

    /**
     * عرض تصاميم المستخدم الحالي فقط
     * 
     * GET /api/designs/my-designs
     */
    public function myDesigns(Request $request): DesignCollection
    {
        $user = $request->user();
        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'sort_by', 'sort_direction', 'per_page'
        ]);

        $designs = $this->designService->getMyDesigns($user->id, $filters);

        return new DesignCollection($designs);
    }

    /**
     * تصفح تصاميم الآخرين (للمستخدمين)
     * 
     * GET /api/designs/browse
     */
    public function browse(Request $request): DesignCollection
    {
        $user = $request->user();
        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'creator_id', 'sort_by', 'sort_direction', 'per_page'
        ]);

        $designs = $this->designService->getOthersDesigns($user->id, $filters);

        return new DesignCollection($designs);
    }

    /**
     * عرض تصميم واحد بالتفاصيل
     * 
     * GET /api/designs/{design}
     */
    public function show(Request $request, Design $design): DesignResource|JsonResponse
    {
        // التحقق من صلاحية العرض
        if (Gate::denies('view', $design)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this design',
                'message_ar' => 'ليس لديك صلاحية لعرض هذا التصميم',
            ], 403);
        }

        $design = $this->designService->getDesignById($design->id);

        return new DesignResource($design);
    }

    /**
     * إنشاء تصميم جديد (للمستخدمين فقط)
     * 
     * POST /api/designs
     */
    public function store(StoreDesignRequest $request): JsonResponse
    {
        $user = $request->user();

        // التحقق من صلاحية الإنشاء
        if (Gate::denies('create', Design::class)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create designs',
                'message_ar' => 'ليس لديك صلاحية لإنشاء تصاميم',
            ], 403);
        }

        $data = $request->validated();
        $data['images'] = $request->file('images', []);

        $design = $this->designService->createDesign($user->id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Design created successfully',
            'message_ar' => 'تم إنشاء التصميم بنجاح',
            'data' => new DesignResource($design),
        ], 201);
    }

    /**
     * تحديث تصميم (صاحب التصميم أو الأدمن فقط)
     * 
     * PUT /api/designs/{design}
     */
    public function update(UpdateDesignRequest $request, Design $design): JsonResponse
    {
        // التحقق من صلاحية التعديل
        if (Gate::denies('update', $design)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this design',
                'message_ar' => 'ليس لديك صلاحية لتعديل هذا التصميم',
            ], 403);
        }

        $data = $request->validated();
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $design = $this->designService->updateDesign($design, $data);

        return response()->json([
            'success' => true,
            'message' => 'Design updated successfully',
            'message_ar' => 'تم تحديث التصميم بنجاح',
            'data' => new DesignResource($design),
        ]);
    }

    /**
     * حذف تصميم (صاحب التصميم أو الأدمن فقط)
     * 
     * DELETE /api/designs/{design}
     */
    public function destroy(Request $request, Design $design): JsonResponse
    {
        // التحقق من صلاحية الحذف
        if (Gate::denies('delete', $design)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this design',
                'message_ar' => 'ليس لديك صلاحية لحذف هذا التصميم',
            ], 403);
        }

        $this->designService->deleteDesign($design);

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully',
            'message_ar' => 'تم حذف التصميم بنجاح',
        ]);
    }

    /**
     * إحصائيات التصاميم (للأدمن فقط)
     * 
     * GET /api/designs/stats
     */
    public function stats(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view statistics',
                'message_ar' => 'ليس لديك صلاحية لعرض الإحصائيات',
            ], 403);
        }

        $stats = $this->designService->getDesignStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * جميع التصاميم للأدمن مع البحث المتقدم
     * 
     * GET /api/admin/designs
     */
    public function adminIndex(Request $request): DesignCollection|JsonResponse
    {
        if (!$this->isAdmin($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied',
                'message_ar' => 'الوصول مرفوض',
            ], 403);
        }

        $filters = $request->only([
            'search', 'size_id', 'min_price', 'max_price',
            'design_option_id', 'user_id', 'is_active',
            'sort_by', 'sort_direction', 'per_page'
        ]);

        $designs = $this->designService->getAllDesigns($filters);

        return new DesignCollection($designs);
    }

    /**
     * التحقق من أن المستخدم أدمن
     */
    private function isAdmin($user): bool
    {
        return $user && in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
    }
}
