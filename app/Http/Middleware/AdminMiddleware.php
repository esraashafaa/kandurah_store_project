<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        $user = auth()->user();
        
        // Check if user has admin or super_admin role
        // Handle both Enum and string values
        $userRole = $user->role instanceof \App\Enums\RoleEnum ? $user->role->value : $user->role;
        
        if (!in_array($userRole, ['admin', 'super_admin'])) {
            abort(403, 'عذراً، ليس لديك صلاحيات للوصول إلى هذه الصفحة. يجب أن تكون مشرفاً.');
        }
        
        return $next($request);
    }
}

