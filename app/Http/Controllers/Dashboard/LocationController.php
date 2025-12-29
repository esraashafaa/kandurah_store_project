<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
{
    /**
     * Service Layer
     */
    protected LocationService $locationService;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    // ═══════════════════════════════════════════════════════
    // 📊 DASHBOARD OPERATIONS
    // ═══════════════════════════════════════════════════════

    /**
     * عرض قائمة جميع المواقع (للمشرفين)
     * GET /dashboard/locations
     * 
     * Query Parameters:
     * - search: البحث في city, area, street, user name
     * - city: فلترة حسب المدينة
     * - area: فلترة حسب المنطقة
     * - user_id: فلترة حسب المستخدم
     * - is_default: فلترة المواقع الافتراضية (true/false)
     * - sort_by: الترتيب (city, area, created_at, user_name)
     * - sort_direction: اتجاه الترتيب (asc, desc)
     * - per_page: عدد العناصر في الصفحة (default: 15)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // التحقق من صلاحية المشرف
        $this->authorize('viewAny', Location::class);

        // جمع الفلاتر من الطلب
        $filters = [
            'search' => $request->input('search'),
            'city' => $request->input('city'),
            'area' => $request->input('area'),
            'user_id' => $request->input('user_id'),
            'is_default' => $request->has('is_default') ? filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN) : null,
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_direction' => $request->input('sort_direction', 'desc'),
            'per_page' => $request->input('per_page', 15),
        ];

        // الحصول على المواقع مع pagination
        $locations = $this->locationService->getAllLocations($filters);
        
        // الحصول على الإحصائيات
        $stats = $this->locationService->getLocationStats();
        
        // الحصول على قائمة المدن للفلترة
        $cities = \App\Models\Location::distinct()->pluck('city')->sort()->values();
        
        // الحصول على قائمة المستخدمين للفلترة
        $users = \App\Models\User::whereHas('locations')->select('id', 'name', 'email')->get();

        return view('admin.locations.index', compact('locations', 'stats', 'cities', 'users'));
    }

    /**
     * عرض إحصائيات المواقع
     * GET /dashboard/locations/stats
     * 
     * Response Format:
     * {
     *   "total_locations": 150,
     *   "total_cities": 10,
     *   "total_users_with_locations": 45,
     *   "locations_by_city": [
     *     {"city": "القاهرة", "count": 50},
     *     {"city": "الإسكندرية", "count": 30},
     *     ...
     *   ]
     * }
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        // الحصول على الإحصائيات من Service
        $stats = $this->locationService->getLocationStats();

        return response()->json($stats);
    }

    /**
     * تصدير المواقع (Export)
     * GET /dashboard/locations/export
     * 
     * يمكن إضافة هذه الدالة لاحقاً لتصدير المواقع كـ CSV أو Excel
     * 
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        // TODO: تنفيذ التصدير لاحقاً
        // يمكن استخدام Laravel Excel Package
        // return Excel::download(new LocationsExport, 'locations.xlsx');
        
        return response()->json([
            'message' => 'Export feature coming soon'
        ], 501); // 501 Not Implemented
    }

    /**
     * الحصول على قائمة المدن المتاحة
     * GET /dashboard/locations/cities
     * 
     * Response Format:
     * {
     *   "cities": ["القاهرة", "الإسكندرية", "الجيزة", ...]
     * }
     * 
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        // الحصول على قائمة المدن الفريدة
        $cities = \App\Models\Location::distinct()
                                      ->pluck('city')
                                      ->sort()
                                      ->values();

        return response()->json([
            'cities' => $cities
        ]);
    }

    /**
     * الحصول على قائمة المناطق في مدينة معينة
     * GET /dashboard/locations/areas?city=القاهرة
     * 
     * Response Format:
     * {
     *   "areas": ["مدينة نصر", "مصر الجديدة", "المعادي", ...]
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAreas(Request $request): JsonResponse
    {
        $request->validate([
            'city' => 'required|string'
        ]);

        // الحصول على المناطق في المدينة المحددة
        $areas = \App\Models\Location::where('city', $request->city)
                                     ->distinct()
                                     ->pluck('area')
                                     ->sort()
                                     ->values();

        return response()->json([
            'areas' => $areas
        ]);
    }

    /**
     * البحث المتقدم في المواقع
     * POST /dashboard/locations/search
     * 
     * Body:
     * {
     *   "query": "القاهرة",
     *   "filters": {
     *     "city": "القاهرة",
     *     "area": "مدينة نصر",
     *     "user_id": 5,
     *     "is_default": true,
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
            'filters.city' => 'nullable|string',
            'filters.area' => 'nullable|string',
            'filters.user_id' => 'nullable|integer|exists:users,id',
            'filters.is_default' => 'nullable|boolean',
            'filters.date_from' => 'nullable|date',
            'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
        ]);

        // دمج البحث والفلاتر
        $searchParams = array_merge(
            $request->input('filters', []),
            ['search' => $request->input('query')]
        );

        // إضافة فلترة حسب التاريخ إذا كانت موجودة
        $query = \App\Models\Location::query()->with('user:id,name,email');

        if (!empty($searchParams['search'])) {
            $query->search($searchParams['search']);
        }

        if (!empty($searchParams['city'])) {
            $query->filterByCity($searchParams['city']);
        }

        if (!empty($searchParams['area'])) {
            $query->filterByArea($searchParams['area']);
        }

        if (!empty($searchParams['user_id'])) {
            $query->where('user_id', $searchParams['user_id']);
        }

        if (isset($searchParams['is_default'])) {
            if ($searchParams['is_default']) {
                $query->onlyDefault();
            } else {
                $query->exceptDefault();
            }
        }

        // فلترة حسب التاريخ
        if (!empty($request->input('filters.date_from'))) {
            $query->whereDate('created_at', '>=', $request->input('filters.date_from'));
        }

        if (!empty($request->input('filters.date_to'))) {
            $query->whereDate('created_at', '<=', $request->input('filters.date_to'));
        }

        // الترتيب والـ Pagination
        $locations = $query->sortBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 15));

        return LocationResource::collection($locations);
    }

    /**
     * عرض موقع واحد (للمشرف)
     * GET /dashboard/locations/{location}
     * 
     * @param Location $location
     * @return LocationResource
     */
    public function show(Location $location): LocationResource
    {
        $this->authorize('view', $location);
        $location->load('user:id,name,email');
        return new LocationResource($location);
    }
}