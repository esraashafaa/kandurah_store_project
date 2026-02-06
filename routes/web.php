<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\DesignController;
use App\Http\Controllers\Dashboard\DesignOptionController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LanguageController;
use App\Enums\OrderStatus;

Route::get('/', function () {
    if (auth()->check() || auth()->guard('admin')->check()) {
        // Admin → dashboard, regular user → my-designs
        if (auth()->guard('admin')->check() || auth()->user() instanceof \App\Models\Admin) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('my-designs.index');
    }
    return redirect()->route('login');
});

// خدمة صور التصاميم من التخزين (يعمل حتى لو الرابط الرمزي لا يعمل على Windows)
Route::get('storage/designs/{filename}', function (string $filename) {
    $path = 'designs/' . $filename;
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $fullPath = Storage::disk('public')->path($path);
    $mime = match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        default => 'application/octet-stream',
    };
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('filename', '[a-zA-Z0-9_.-]+')->name('storage.designs');

// Language Switcher - تبديل اللغة
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', 'en|ar');

// Admin Dashboard - الصفحة الرئيسية للوحة التحكم
Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['admin'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ═══════════════════════════════════════════════════════
    // 🎨 USER DESIGN MANAGEMENT - إدارة التصاميم للمستخدمين
    // ═══════════════════════════════════════════════════════
    
    Route::prefix('my-designs')->name('my-designs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\DesignController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\User\DesignController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\User\DesignController::class, 'store'])->name('store');
        Route::get('/{design}', [\App\Http\Controllers\User\DesignController::class, 'show'])->name('show');
        Route::get('/{design}/edit', [\App\Http\Controllers\User\DesignController::class, 'edit'])->name('edit');
        Route::put('/{design}', [\App\Http\Controllers\User\DesignController::class, 'update'])->name('update');
        Route::delete('/{design}', [\App\Http\Controllers\User\DesignController::class, 'destroy'])->name('destroy');
    });
    
    // تصفح تصاميم الآخرين
    Route::prefix('designs')->name('designs.')->group(function () {
        Route::get('/browse', [\App\Http\Controllers\User\DesignController::class, 'browse'])->name('browse');
    });

    // إشعارات المستخدم (إشعاراتي فقط)
    Route::prefix('my/notifications')->name('my.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('index');
        Route::put('/{id}/mark-read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [\App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';

// ═══════════════════════════════════════════════════════
// 👥 ADMIN ROUTES - إدارة المستخدمين والطلبات والمحتوى
// ═══════════════════════════════════════════════════════

Route::prefix('admin')
     ->middleware(['admin'])
     ->name('admin.')
     ->group(function () {
    
    // إدارة المستخدمين
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', function () {
            $query = \App\Models\User::query();
            
            // البحث
            if (request()->has('search') && request('search')) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            // فلترة حسب الحالة
            if (request()->has('status') && request('status')) {
                if (request('status') === 'active') {
                    $query->where('is_active', true);
                } elseif (request('status') === 'inactive') {
                    $query->where('is_active', false);
                }
            }
            
            $users = $query->latest()->paginate(10)->withQueryString();
            
            $stats = [
                'total' => \App\Models\User::count(),
                'active' => \App\Models\User::where('is_active', true)->count(),
                'admins' => \App\Models\Admin::count(),
                'new_today' => \App\Models\User::whereDate('created_at', today())->count(),
            ];
            return view('admin.users.index', compact('users', 'stats'));
        })->name('index');
        
        Route::get('/create', function () {
            $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
                ->orderBy('name')
                ->get()
                ->groupBy(function ($permission) {
                    $parts = explode('.', $permission->name);
                    return $parts[0] ?? 'other';
                });
            return view('admin.users.create', compact('permissionGroups', 'permissions'));
        })->name('create');
        
        Route::post('/store', function () {
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'is_active' => 'nullable|boolean',
                'email_verified' => 'nullable|boolean',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'wallet_balance' => 'nullable|numeric|min:0',
            ]);
            
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'is_active' => request()->has('is_active') ? true : false,
                'wallet_balance' => $validated['wallet_balance'] ?? 0,
            ]);
            
            // رفع الصورة الشخصية
            if (request()->hasFile('profile_image')) {
                $avatarPath = request()->file('profile_image')->store('avatars', 'public');
                $user->avatar = $avatarPath;
                $user->save();
            }
            
            // تأكيد البريد الإلكتروني إذا تم اختياره
            if (request()->has('email_verified')) {
                $user->email_verified_at = now();
                $user->save();
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'تم إضافة المستخدم بنجاح');
        })->name('store');
        
        Route::get('/{user}', function ($userId) {
            $user = \App\Models\User::with(['orders', 'designs', 'locations', 'transactions'])->findOrFail($userId);
            
            // الحصول على الصلاحيات والمجموعات
            $userPermissions = $user->getAllPermissions();
            $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
            
            // تحديد المجموعات التي يمتلك المستخدم جميع صلاحياتها
            $userGroups = [];
            foreach ($permissionGroups as $group) {
                $groupPermissionIds = $group->permissions->pluck('id')->toArray();
                $userPermissionIds = $userPermissions->pluck('id')->toArray();
                if (!empty($groupPermissionIds) && count(array_intersect($groupPermissionIds, $userPermissionIds)) === count($groupPermissionIds)) {
                    $userGroups[] = $group;
                }
            }
            
            return view('admin.users.show', compact('user', 'userPermissions', 'userGroups'));
        })->name('show');
        
        Route::get('/{user}/edit', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
                ->orderBy('name')
                ->get()
                ->groupBy(function ($permission) {
                    $parts = explode('.', $permission->name);
                    return $parts[0] ?? 'other';
                });
            
            // الحصول على الصلاحيات الحالية للمستخدم
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();
            
            // تحديد المجموعات التي يمتلك المستخدم جميع صلاحياتها
            $userGroups = [];
            foreach ($permissionGroups as $group) {
                $groupPermissionIds = $group->permissions->pluck('id')->toArray();
                if (!empty($groupPermissionIds) && count(array_intersect($groupPermissionIds, $userPermissions)) === count($groupPermissionIds)) {
                    $userGroups[] = $group->id;
                }
            }
            
            return view('admin.users.edit', compact('user', 'permissionGroups', 'permissions', 'userPermissions', 'userGroups'));
        })->name('edit');
        
        Route::put('/{user}', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:8|confirmed',
                'is_active' => 'nullable|boolean',
                'email_verified' => 'nullable|boolean',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'wallet_balance' => 'nullable|numeric|min:0',
            ]);
            
            // تحديث البيانات الأساسية
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            $user->is_active = request()->has('is_active') ? true : false;
            $user->wallet_balance = $validated['wallet_balance'] ?? $user->wallet_balance;
            
            // تحديث كلمة المرور إذا تم إدخالها
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }
            
            // رفع الصورة الشخصية إذا تم اختيارها
            if (request()->hasFile('profile_image')) {
                // حذف الصورة القديمة إن وجدت
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $avatarPath = request()->file('profile_image')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }
            
            // تأكيد البريد الإلكتروني إذا تم اختياره
            if (request()->has('email_verified')) {
                $user->email_verified_at = now();
            } else {
                // إلغاء التأكيد إذا تم إلغاء الاختيار
                $user->email_verified_at = null;
            }
            
            $user->save();
            
            return redirect()->route('admin.users.show', $user)
                ->with('success', 'تم تحديث المستخدم بنجاح');
        })->name('update');
        
        Route::get('/admins', function () {
            return redirect()->route('admin.admins.index');
        })->name('admins');
        
        // AJAX: Toggle User Status
        Route::post('/{user}/toggle-status', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            
            // منع المستخدم من تعطيل نفسه
            if (auth()->id() == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك تعطيل حسابك الخاص'
                ], 403);
            }
            
            $user->is_active = !$user->is_active;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة المستخدم بنجاح',
                'is_active' => $user->is_active
            ]);
        })->name('toggle-status');
        
        // AJAX: Add Wallet Balance
        Route::post('/{user}/add-balance', function ($userId) {
            $validated = request()->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
            ]);
            
            $user = \App\Models\User::findOrFail($userId);
            
            $transaction = $user->addFunds(
                $validated['amount'],
                $validated['description'] ?? 'إضافة رصيد من قبل المشرف'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الرصيد بنجاح',
                'balance' => $user->fresh()->wallet_balance,
                'transaction' => $transaction
            ]);
        })->name('add-balance');
        
        // AJAX: Reset Password
        Route::post('/{user}/reset-password', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            
            // إنشاء رمز إعادة تعيين كلمة المرور
            $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
            
            // إرسال رابط إعادة التعيين
            $user->sendPasswordResetNotification($token);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريد المستخدم'
            ]);
        })->name('reset-password');
        
        // AJAX: Delete User
        Route::delete('/{user}', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            
            // تحقق من أن المستخدم لا يحذف نفسه
            if (auth()->id() == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك حذف حسابك الخاص'
                ], 403);
            }
            
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المستخدم بنجاح'
            ]);
        })->name('destroy');
    });
    
    // إدارة المشرفين (Admins)
    // إدارة الصلاحيات لجميع المشرفين (يجب أن يكون قبل resource)
    Route::get('admins/permissions/manage', [\App\Http\Controllers\Admin\AdminController::class, 'managePermissions'])->name('admins.permissions');
    
    Route::resource('admins', \App\Http\Controllers\Admin\AdminController::class);
    
    // إدارة مجموعات الصلاحيات (فقط للسوبر أدمن)
    Route::prefix('permission-groups')->name('permission-groups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'store'])->name('store');
        Route::get('/{permissionGroup}/edit', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'edit'])->name('edit');
        Route::put('/{permissionGroup}', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'update'])->name('update');
        Route::delete('/{permissionGroup}', [\App\Http\Controllers\Admin\PermissionGroupController::class, 'destroy'])->name('destroy');
    });
    
    // إدارة الكوبونات
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'show'])->name('show');
        Route::get('/{coupon}/edit', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('destroy');
        Route::post('/{coupon}/toggle', [\App\Http\Controllers\Admin\CouponController::class, 'toggle'])->name('toggle');
    });
    
    // إدارة التقييمات
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', function () {
            $query = \App\Models\Review::with(['user', 'order']);
            
            // البحث
            if (request('search')) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('comment', 'like', "%{$search}%");
                });
            }
            
            // فلترة حسب التقييم
            if (request('rating')) {
                $query->where('rating', request('rating'));
            }
            
            $reviews = $query->latest()->paginate(15)->withQueryString();
            
            // حساب الإحصائيات
            $stats = [
                'total' => \App\Models\Review::count(),
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'average' => \App\Models\Review::avg('rating') ?? 0,
            ];
            
            return view('admin.reviews.index', compact('reviews', 'stats'));
        })->name('index');
    });
    
    // إدارة الإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::post('/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('send');
        Route::post('/test', [\App\Http\Controllers\Admin\NotificationController::class, 'sendTest'])->name('test');
        Route::put('/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
    });
    
    // الإعدادات والتقارير
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');
});

// ═══════════════════════════════════════════════════════
// 🎨 DASHBOARD ROUTES - إدارة التصاميم والخيارات
// ═══════════════════════════════════════════════════════

Route::prefix('dashboard')
     ->middleware(['admin']) // تحتاج تسجيل دخول + صلاحية مشرف
     ->name('dashboard.')
     ->group(function () {
    
    // ═══════════════════════════════════════════════════════
    // 📍 LOCATION MANAGEMENT
    // ═══════════════════════════════════════════════════════
    
    /**
     * Dashboard Location Routes
     * 
     * GET  /dashboard/locations                → index() (قائمة جميع المواقع)
     * GET  /dashboard/locations/{id}           → show()  (عرض موقع واحد)
     */
    Route::prefix('locations')->name('locations.')->group(function () {
        
        // قائمة جميع المواقع
        Route::get('/', [LocationController::class, 'index'])
             ->name('index');
        
        // عرض موقع واحد
        Route::get('{location}', [LocationController::class, 'show'])
             ->name('show');
        
        // إحصائيات المواقع
        Route::get('stats/overview', [LocationController::class, 'stats'])
             ->name('stats');
        
        // قائمة المدن
        Route::get('data/cities', [LocationController::class, 'getCities'])
             ->name('cities');
        
        // قائمة المناطق في مدينة
        Route::get('data/areas', [LocationController::class, 'getAreas'])
             ->name('areas');
        
        // بحث متقدم
        Route::post('search', [LocationController::class, 'advancedSearch'])
             ->name('search');
        

    });

    // ═══════════════════════════════════════════════════════
    // 🎨 DESIGN MANAGEMENT
    // ═══════════════════════════════════════════════════════
    
    /**
     * Dashboard Design Routes
     * 
     * GET  /dashboard/designs                → index() (قائمة جميع التصاميم)
     * GET  /dashboard/designs/{id}           → show()  (عرض تصميم واحد)
     * GET  /dashboard/designs/stats          → stats() (إحصائيات التصاميم)
     * POST /dashboard/designs/search         → advancedSearch() (بحث متقدم)
     */
    Route::prefix('designs')->name('designs.')->group(function () {
        
        // قائمة جميع التصاميم
        Route::get('/', function () {
            $query = \App\Models\Design::with(['user', 'images'])->withCount('orderItems');
            
            // البحث
            if (request()->has('search') && request('search')) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"])
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            // فلتر حسب المقاس
            if (request()->has('size_id') && request('size_id')) {
                $query->whereHas('sizes', function($q) {
                    $q->where('sizes.id', request('size_id'));
                });
            }
            
            // فلتر حسب نطاق السعر
            if (request()->has('min_price') && request('min_price')) {
                $query->where('price', '>=', request('min_price'));
            }
            if (request()->has('max_price') && request('max_price')) {
                $query->where('price', '<=', request('max_price'));
            }
            
            // فلتر حسب خيار التصميم
            if (request()->has('design_option_id') && request('design_option_id')) {
                $query->whereHas('designOptions', function($q) {
                    $q->where('design_options.id', request('design_option_id'));
                });
            }
            
            // فلتر حسب المستخدم
            if (request()->has('user_id') && request('user_id')) {
                $query->where('user_id', request('user_id'));
            }
            
            // فلتر حسب الحالة
            if (request()->has('is_active') && request('is_active') !== '') {
                $query->where('is_active', request('is_active'));
            }
            
            $designs = $query->latest()->paginate(12)->withQueryString();
            
            // البيانات للفلاتر
            $sizes = \App\Models\Size::active()->ordered()->get();
            $designOptions = \App\Models\DesignOption::active()->get();
            $users = \App\Models\User::whereHas('designs')->select('id', 'name')->get();
            
            $stats = [
                'total' => \App\Models\Design::count(),
                'today' => \App\Models\Design::whereDate('created_at', today())->count(),
                'with_orders' => \App\Models\Design::has('orderItems')->count(),
                'unique_users' => \App\Models\Design::distinct('user_id')->count('user_id'),
            ];
            
            return view('admin.designs.index', compact('designs', 'stats', 'sizes', 'designOptions', 'users'));
        })->name('index');
        
        // عرض تصميم واحد
        Route::get('{design}', function ($designId) {
            $design = \App\Models\Design::with(['user', 'images', 'designOptions', 'sizes'])->withCount('orderItems')->findOrFail($designId);
            return view('admin.designs.show', compact('design'));
        })->name('show');
        
        // إحصائيات التصاميم
        Route::get('stats/overview', [DesignController::class, 'stats'])
             ->name('stats');
        
        // بحث متقدم
        Route::post('search', [DesignController::class, 'advancedSearch'])
             ->name('search');
    });

    // ═══════════════════════════════════════════════════════
    // 🎨 DESIGN OPTION MANAGEMENT
    // ═══════════════════════════════════════════════════════
    
    /**
     * Dashboard Design Option Routes
     * 
     * GET    /dashboard/design-options                → index() (قائمة جميع الخيارات)
     * GET    /dashboard/design-options/{id}           → show()  (عرض خيار واحد)
     * POST   /dashboard/design-options                → store() (إنشاء خيار جديد)
     * PUT    /dashboard/design-options/{id}           → update() (تحديث خيار)
     * PATCH  /dashboard/design-options/{id}           → update() (تحديث خيار)
     * DELETE /dashboard/design-options/{id}           → destroy() (حذف خيار)
     * GET    /dashboard/design-options/stats          → stats() (إحصائيات)
     * GET    /dashboard/design-options/types          → types() (أنواع الخيارات)
     * GET    /dashboard/design-options/grouped        → grouped() (خيارات مجمعة)
     */
    Route::prefix('design-options')->name('design-options.')->group(function () {
        
        // قائمة جميع خيارات التصميم
        Route::get('/', function () {
            $options = \App\Models\DesignOption::latest()->paginate(12);
            $stats = [
                'total' => \App\Models\DesignOption::count(),
                'active' => \App\Models\DesignOption::where('is_active', true)->count(),
                'types' => \App\Models\DesignOption::distinct('type')->count('type'),
                'used' => \App\Models\DesignOption::has('designs')->count(),
            ];
            return view('admin.design-options.index', compact('options', 'stats'));
        })->name('index');
        
        // عرض نموذج إنشاء خيار تصميم جديد
        Route::get('/create', function () {
            // التحقق من صلاحية إنشاء خيارات التصميم (فقط الأدمن والسوبر أدمن)
            $user = auth()->user();
            abort_unless($user && $user->can('create', \App\Models\DesignOption::class), 403, 'عذراً، ليس لديك صلاحيات لإنشاء خيارات التصميم. يجب أن تكون مشرفاً.');
            
            $types = \App\Enums\DesignOptionTypeEnum::options();
            return view('admin.design-options.create', compact('types'));
        })->name('create');
        
        // حفظ خيار تصميم جديد
        Route::post('/', [DesignOptionController::class, 'store'])
             ->name('store');
        
        // عرض خيار تصميم واحد
        Route::get('{designOption}', function ($designOptionId) {
            $option = \App\Models\DesignOption::with('designs')->findOrFail($designOptionId);
            $types = \App\Enums\DesignOptionTypeEnum::options();
            return view('admin.design-options.show', compact('option', 'types'));
        })->name('show');
        
        // عرض نموذج تعديل خيار تصميم
        Route::get('{designOption}/edit', function ($designOptionId) {
            $option = \App\Models\DesignOption::findOrFail($designOptionId);
            $types = \App\Enums\DesignOptionTypeEnum::options();
            return view('admin.design-options.edit', compact('option', 'types'));
        })->name('edit');
        
        // تحديث خيار تصميم
        Route::put('{designOption}', [DesignOptionController::class, 'update'])
             ->name('update');
        Route::patch('{designOption}', [DesignOptionController::class, 'update'])
             ->name('update');
        
        // حذف خيار تصميم
        Route::delete('{designOption}', [DesignOptionController::class, 'destroy'])
             ->name('destroy');
        
        // AJAX: Toggle Option Status
        Route::post('{designOption}/toggle', function ($designOptionId) {
            $option = \App\Models\DesignOption::findOrFail($designOptionId);
            
            $option->is_active = !$option->is_active;
            $option->save();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الخيار بنجاح',
                'is_active' => $option->is_active
            ]);
        })->name('toggle');
        
        // إحصائيات خيارات التصميم
        Route::get('stats/overview', [DesignOptionController::class, 'stats'])
             ->name('stats');
        
        // أنواع الخيارات المتاحة
        Route::get('data/types', [DesignOptionController::class, 'types'])
             ->name('types');
        
        // خيارات التصميم مجمعة حسب النوع
        Route::get('data/grouped', [DesignOptionController::class, 'grouped'])
             ->name('grouped');
    });

    // ═══════════════════════════════════════════════════════
    // 🛒 ORDER MANAGEMENT
    // ═══════════════════════════════════════════════════════
    
    /**
     * Dashboard Order Routes
     * 
     * GET  /dashboard/orders                → index() (قائمة جميع الطلبات)
     * GET  /dashboard/orders/create         → create() (صفحة إنشاء طلب)
     * POST /dashboard/orders                → store() (حفظ طلب جديد)
     * GET  /dashboard/orders/{order}        → show() (عرض تفاصيل طلب)
     */
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\OrderController::class, 'index'])->name('index');
        
        // إنشاء طلب جديد
        Route::get('/create', [\App\Http\Controllers\Dashboard\OrderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\OrderController::class, 'store'])->name('store');
        
        Route::get('/pending', function () {
            return redirect()->route('dashboard.orders.index', ['status' => 'pending']);
        })->name('pending');
        
        Route::get('/processing', function () {
            return redirect()->route('dashboard.orders.index', ['status' => 'processing']);
        })->name('processing');
        
        Route::get('/completed', function () {
            return redirect()->route('dashboard.orders.index', ['status' => 'completed']);
        })->name('completed');
        
        // Stats (يجب أن يكون قبل {order} routes)
        Route::get('/stats/overview', [\App\Http\Controllers\Dashboard\OrderController::class, 'stats'])->name('stats');
        
        // AJAX: Update Order Status (يجب أن يكون قبل {order} route)
        Route::put('/{order}/status', [\App\Http\Controllers\Dashboard\OrderController::class, 'updateStatus'])->name('update-status');
        
        // AJAX: Cancel Order (يجب أن يكون قبل {order} route)
        Route::post('/{order}/cancel', [\App\Http\Controllers\Dashboard\OrderController::class, 'cancel'])->name('cancel');
        
        // Invoice Routes (يجب أن يكون قبل {order} route)
        Route::get('/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/{order}/invoice/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
        Route::get('/{order}/invoice/view', [\App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');
        
        // Show Order (يجب أن يكون في النهاية)
        Route::get('/{order}', [\App\Http\Controllers\Dashboard\OrderController::class, 'show'])->name('show');
        
        // Export Orders
        Route::get('/export', function () {
            // TODO: استخدام Laravel Excel لتصدير البيانات
            
            return response()->json([
                'message' => 'ميزة التصدير قيد التطوير'
            ]);
        })->name('export');
        
        // Get user locations (AJAX)
        Route::get('/user/{user}/locations', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            $locations = $user->locations()->get();
            
            return response()->json([
                'success' => true,
                'data' => $locations->map(function($location) {
                    return [
                        'id' => $location->id,
                        'city' => $location->city,
                        'area' => $location->area,
                        'street' => $location->street,
                        'house_number' => $location->house_number,
                        'full_address' => $location->city . ' - ' . $location->area . ' - ' . $location->street,
                    ];
                })
            ]);
        })->name('user-locations');
    });
});
Route::middleware(['auth'])->group(function () {
     // دفع شحن المحفظة فقط (للمستخدمين)
     Route::get('/payment', [StripeController::class, 'showPaymentForm'])
         ->name('payment.form');
     
     Route::post('/stripe/checkout', [StripeController::class, 'checkout'])
         ->name('stripe.checkout');
     
     Route::get('/stripe/success', [StripeController::class, 'success'])
         ->name('stripe.success');
     
     Route::get('/stripe/cancel', [StripeController::class, 'cancel'])
         ->name('stripe.cancel');

     // دفع طلب معين (عودة من Stripe بعد الدفع)
     Route::get('/stripe/order/{order}/pay', [StripeController::class, 'payOrder'])
         ->name('stripe.order.pay');
     Route::get('/stripe/order/{order}/success', [StripeController::class, 'orderSuccess'])
         ->name('stripe.order.success');
     Route::get('/stripe/order/{order}/cancel', [StripeController::class, 'orderCancel'])
         ->name('stripe.order.cancel');
 });
 
 // Webhook Route - بدون CSRF Token
 Route::post('/stripe/webhook', [WebhookController::class, 'handle'])
     ->withoutMiddleware([
         \App\Http\Middleware\VerifyCsrfToken::class,
     ])
     ->name('stripe.webhook');