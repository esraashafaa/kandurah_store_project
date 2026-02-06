<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Authorize a given action for the current user.
     * This method ensures that Admin users from guard 'admin' are properly authorized.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        // الحصول على Admin من guard 'admin' أو من guard الافتراضي
        $user = auth()->guard('admin')->user() ?? auth()->user();
        
        // إذا كان هناك Admin من guard 'admin'، نستخدمه للـ authorize
        if ($user && $user instanceof \App\Models\Admin) {
            return Gate::forUser($user)->authorize($ability, $arguments);
        }
        
        // إذا لم يكن Admin، نستخدم الـ authorize العادي من AuthorizesRequests trait
        return Gate::authorize($ability, $arguments);
    }
}
