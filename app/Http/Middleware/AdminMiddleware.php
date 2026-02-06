<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
        // التحقق من تسجيل الدخول في guard الافتراضي (web)
        else if (auth()->check()) {
            $user = auth()->user();
            
            // التحقق من أن المستخدم هو Admin
            if ($user instanceof Admin) {
                $admin = $user;
            }
        }
        
        // إذا لم يكن مسجل دخول أو ليس admin
        if (!$admin) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // حفظ Admin ID في session لاستخدامه في views
        // هذا يضمن أن auth()->user() سيعيد Admin بدلاً من البحث في جدول users
        $request->session()->put('admin_id', $admin->id);
        $request->session()->put('admin_guard', 'admin');
        
        // تعيين Admin في guard web باستخدام loginUsingId مع provider admins
        // نحتاج لاستخدام provider admins بدلاً من users
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()?->id !== $admin->id) {
            // استخدام loginUsingId مع guard admin ثم نسخه لـ web
            Auth::guard('admin')->setUser($admin);
            
            // محاولة تعيين Admin في guard web
            // لكن guard web يستخدم provider users، لذا سنستخدم session بدلاً من ذلك
            // في views سنستخدم helper function للحصول على Admin
        }
        
        return $next($request);
    }
}

