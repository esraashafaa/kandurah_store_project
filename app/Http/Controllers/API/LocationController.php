<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{

    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }


    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Location::class);
        
        $locations = $this->locationService->getUserLocations(
            auth()->id(),
            $request->all() 
        );

        return LocationResource::collection($locations);
    }


    public function show(Location $location): LocationResource
    {
        $this->authorize('view', $location);
  
        return new LocationResource($location->load('user'));
    }


    public function store(StoreLocationRequest $request): JsonResponse
    {
        $this->authorize('create', Location::class);
        
        $location = $this->locationService->createLocation(
            auth()->id(),
            $request->validated() 
        );
        return (new LocationResource($location))
            ->response()
            ->setStatusCode(201); 
    }


    public function update(UpdateLocationRequest $request, Location $location): LocationResource
    {
        $this->authorize('update', $location);
        
        $updatedLocation = $this->locationService->updateLocation(
            $location,
            $request->validated()
        );

        return new LocationResource($updatedLocation);
    }

    public function destroy(Location $location): JsonResponse
    {
        $this->authorize('delete', $location);
        
        $this->locationService->deleteLocation($location);

        return response()->json([
            'message' => 'Location deleted successfully',
        ], 200);
    }

    /**
     * الحصول على الموقع الافتراضي للمستخدم
     * GET /api/locations/default/get
     * 
     * @return LocationResource|JsonResponse
     */
    public function getDefaultLocation(): LocationResource|JsonResponse
    {
        $location = $this->locationService->getUserDefaultLocation(auth()->id());
        
        if (!$location) {
            return response()->json([
                'message' => 'No default location found'
            ], 404);
        }

        return new LocationResource($location->load('user'));
    }

    /**
     * تعيين موقع كافتراضي
     * POST /api/locations/{location}/set-default
     * 
     * @param Location $location
     * @return LocationResource
     */
    public function setAsDefault(Location $location): LocationResource
    {
        $this->authorize('update', $location);

        $updatedLocation = $this->locationService->updateLocation(
            $location,
            ['is_default' => true]
        );

        return new LocationResource($updatedLocation);
    }

    /**
     * إحصائيات مواقع المستخدم
     * GET /api/my-locations/stats
     * 
     * @return JsonResponse
     */
    public function myStats(): JsonResponse
    {
        $userId = auth()->id();
        
        $stats = [
            'total_locations' => Location::where('user_id', $userId)->count(),
            'default_location' => Location::where('user_id', $userId)
                ->where('is_default', true)
                ->exists(),
            'locations_by_city' => Location::where('user_id', $userId)
                ->select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * الحصول على قائمة المدن المتاحة
     * GET /api/cities
     * 
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        $cities = Location::distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return response()->json([
            'cities' => $cities
        ]);
    }

    /**
     * الحصول على قائمة المناطق في مدينة معينة
     * GET /api/cities/{city}/areas
     * 
     * @param string $city
     * @return JsonResponse
     */
    public function getAreas(string $city): JsonResponse
    {
        $areas = Location::where('city', $city)
            ->distinct()
            ->pluck('area')
            ->sort()
            ->values();

        return response()->json([
            'city' => $city,
            'areas' => $areas
        ]);
    }
}