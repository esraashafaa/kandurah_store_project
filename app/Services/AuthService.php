<?php

namespace App\Services;

use App\Events\Users\UserRegistered;
use App\Models\User;
use App\Models\Admin;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        // تفعيل حدث تسجيل مستخدم جديد
        Event::dispatch(new UserRegistered($user));
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials, string $guard = 'api'): array
    {
        // API guard (Sanctum) لا يدعم attempt() — نتحقق من بيانات الدخول يدوياً
        if ($guard === 'api') {
            $user = User::where('email', $credentials['email'])->first();
            if ($user && Hash::check($credentials['password'], $user->password)) {
                if (!$user->is_active) {
                    throw ValidationException::withMessages([
                        'email' => ['حسابك غير مفعل'],
                    ]);
                }
                $user->updateLastLogin();
                $token = $user->createToken('auth_token')->plainTextToken;
                return [
                    'user' => $user,
                    'token' => $token,
                ];
            }
        } else {
            // guard الجلسة (web) يدعم attempt()
            $authGuard = Auth::guard('web');
            if ($authGuard->attempt($credentials)) {
                $user = $authGuard->user();
                if (!$user->is_active) {
                    $authGuard->logout();
                    throw ValidationException::withMessages([
                        'email' => ['حسابك غير مفعل'],
                    ]);
                }
                $user->updateLastLogin();
                return [
                    'user' => $user,
                ];
            }
        }
        
        // محاولة تسجيل الدخول كـ Admin (فقط لـ web)
        if ($guard === 'web') {
            $admin = Admin::where('email', $credentials['email'])->first();
            
            if ($admin && Hash::check($credentials['password'], $admin->password)) {
                if (!$admin->is_active) {
                    throw ValidationException::withMessages([
                        'email' => ['حسابك غير مفعل'],
                    ]);
                }
                
                $admin->updateLastLogin();
                
                // تسجيل دخول Admin في session
                Auth::guard('admin')->login($admin);
                
                return [
                    'admin' => $admin,
                ];
            }
        }
        
        throw ValidationException::withMessages([
            'email' => ['بيانات الدخول غير صحيحة'],
        ]);
    }

    public function logout($user): bool
    {
        if ($user instanceof User) {
            $user->currentAccessToken()->delete();
        }
        return true;
    }
}