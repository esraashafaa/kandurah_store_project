<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        
        // جمع الفلاتر من الطلب
        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'is_active' => $request->has('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : null,
            'per_page' => $request->input('per_page', 15),
        ];
        
        $users = $this->userService->getAllUsers($filters);
        
        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $updated = $this->userService->updateUser($user, $request->validated());
        
        return response()->json([
            'message' => 'تم التحديث بنجاح',
            'data' => new UserResource($updated)
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        
        $this->userService->deleteUser($user);
        
        return response()->json([
            'message' => 'تم الحذف بنجاح'
        ]);
    }

    /**
     * الحصول على معلومات المستخدم الحالي
     * GET /api/user/me
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * تحديث معلومات المستخدم الحالي
     * PUT/PATCH /api/user/me
     * 
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function updateMe(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        
        // السماح للمستخدم بتحديث بياناته فقط (بدون role و is_active)
        $validated = $request->validated();
        
        // إزالة الحقول التي لا يمكن للمستخدم العادي تحديثها
        unset($validated['role'], $validated['is_active']);
        
        $updated = $this->userService->updateUser($user, $validated);
        
        return response()->json([
            'message' => 'تم تحديث معلوماتك بنجاح',
            'data' => new UserResource($updated)
        ]);
    }
}