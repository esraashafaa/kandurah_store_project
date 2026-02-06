<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = null;
        
        // التحقق من تسجيل الدخول كـ Admin أولاً
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        }
        // التحقق من تسجيل الدخول في guard الافتراضي
        else if ($request->user() instanceof Admin) {
            $admin = $request->user();
        }

        // التحقق من وجود مستخدم مسجل دخول
        if (!$admin) {
            return redirect()->route('login');
        }
        
        // تعيين Admin في guard الافتراضي أيضاً حتى يعمل auth()->user() في views و controllers
        if (!Auth::guard('web')->check() || Auth::guard('web')->user() !== $admin) {
            Auth::guard('web')->setUser($admin);
        }

        return $next($request);
    }
}

