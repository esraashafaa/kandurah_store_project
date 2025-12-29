<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * عرض قائمة المستخدمين
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);
        
        $users = $this->userService->getAllUsers();
        
        return view('dashboard.users.index', compact('users'));
    }

    /**
     * صفحة إنشاء مستخدم جديد
     */
    public function create(): View
    {
        $this->authorize('create', User::class);
        
        return view('dashboard.users.create');
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);
        
        $this->userService->createUser($request->validated());
        
        return redirect()
            ->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * عرض تفاصيل مستخدم
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);
        
        return view('dashboard.users.show', compact('user'));
    }

    /**
     * صفحة تعديل مستخدم
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        
        return view('dashboard.users.edit', compact('user'));
    }

    /**
     * تحديث مستخدم
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        
        $this->userService->updateUser($user, $request->validated());
        
        return redirect()
            ->route('users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * حذف مستخدم
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);
        
        $this->userService->deleteUser($user);
        
        return redirect()
            ->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}