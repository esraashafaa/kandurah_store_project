<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
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
        $user = $request->user();

        // التحقق من وجود مستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login');
        }

        // التحقق من أن المستخدم لديه صلاحية Admin أو Super Admin
        if (!in_array($user->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN])) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        return $next($request);
    }
}

