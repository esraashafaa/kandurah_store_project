<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller للمقاسات
 * المقاسات ثابتة (XS, S, M, L, XL, XXL) ولا يمكن إنشاؤها أو تعديلها
 * فقط العرض متاح
 */
class SizeController extends Controller
{
    /**
     * عرض جميع المقاسات المتاحة
     * 
     * GET /api/sizes
     * 
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Size::ordered();

        // فلتر المقاسات النشطة فقط (الافتراضي)
        if ($request->boolean('active_only', true)) {
            $query->active();
        }

        return SizeResource::collection($query->get());
    }

    /**
     * عرض مقاس واحد
     * 
     * GET /api/sizes/{size}
     * 
     * @param Size $size
     * @return SizeResource
     */
    public function show(Size $size): SizeResource
    {
        return new SizeResource($size);
    }

    /**
     * المقاسات ثابتة - لا يمكن إنشاء مقاسات جديدة
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sizes are fixed and cannot be created. Available sizes: XS, S, M, L, XL, XXL',
            'message_ar' => 'المقاسات ثابتة ولا يمكن إنشاء مقاسات جديدة. المقاسات المتاحة: XS, S, M, L, XL, XXL',
        ], 403);
    }

    /**
     * المقاسات ثابتة - لا يمكن تعديلها
     */
    public function update(Request $request, Size $size): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sizes are fixed and cannot be modified',
            'message_ar' => 'المقاسات ثابتة ولا يمكن تعديلها',
        ], 403);
    }

    /**
     * المقاسات ثابتة - لا يمكن حذفها
     */
    public function destroy(Size $size): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sizes are fixed and cannot be deleted',
            'message_ar' => 'المقاسات ثابتة ولا يمكن حذفها',
        ], 403);
    }
}
