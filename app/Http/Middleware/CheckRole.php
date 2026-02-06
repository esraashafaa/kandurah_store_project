<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = null;
        
        // التحقق من تسجيل الدخول كـ Admin أولاً
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } 
        // التحقق من تسجيل الدخول في guard الافتراضي
        else if (auth()->check()) {
            $user = auth()->user();
            if ($user instanceof Admin) {
                $admin = $user;
            }
        }
        
        // إذا لم يكن مسجل دخول أو ليس admin
        if (!$admin) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // تعيين Admin في guard الافتراضي أيضاً حتى يعمل auth()->user() في views و controllers
        if (!Auth::guard('web')->check() || Auth::guard('web')->user() !== $admin) {
            Auth::guard('web')->setUser($admin);
        }
        
        // الحصول على دور Admin
        $userRole = $admin->role->value;

        // التحقق من أن Admin لديه أحد الأدوار المطلوبة
        if (!in_array($userRole, $roles)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}

