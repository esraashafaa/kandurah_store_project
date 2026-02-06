<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeasurementRequest;
use App\Http\Requests\UpdateMeasurementRequest;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use App\Services\MeasurementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MeasurementController extends Controller
{
    public function __construct(
        private MeasurementService $measurementService
    ) {}

    /**
     * عرض جميع مقاسات المستخدم
     * 
     * GET /api/measurements
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Measurement::class);

        $measurements = $this->measurementService->getUserMeasurements(
            $request->user()->id,
            $request->all()
        );

        return MeasurementResource::collection($measurements);
    }

    /**
     * إنشاء قياس جديد
     * 
     * POST /api/measurements
     */
    public function store(StoreMeasurementRequest $request): JsonResponse
    {
        $this->authorize('create', Measurement::class);

        $measurement = $this->measurementService->createMeasurement(
            $request->user()->id,
            $request->validated()
        );

        return (new MeasurementResource($measurement))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * عرض قياس محدد
     * 
     * GET /api/measurements/{id}
     */
    public function show(Measurement $measurement): MeasurementResource
    {
        $this->authorize('view', $measurement);

        return new MeasurementResource($measurement);
    }

    /**
     * تحديث قياس
     * 
     * PUT /api/measurements/{id}
     */
    public function update(UpdateMeasurementRequest $request, Measurement $measurement): MeasurementResource
    {
        $this->authorize('update', $measurement);

        $updatedMeasurement = $this->measurementService->updateMeasurement(
            $measurement,
            $request->validated()
        );

        return new MeasurementResource($updatedMeasurement);
    }

    /**
     * حذف قياس
     * 
     * DELETE /api/measurements/{id}
     */
    public function destroy(Measurement $measurement): JsonResponse
    {
        $this->authorize('delete', $measurement);

        $this->measurementService->deleteMeasurement($measurement);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القياس بنجاح',
            'message_ar' => 'تم حذف القياس بنجاح',
        ]);
    }
}
