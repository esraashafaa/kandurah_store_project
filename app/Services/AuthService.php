<?php

namespace App\Services;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'role' => RoleEnum::USER,
            'is_active' => true,
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials, string $guard = 'api'): array
    {
        // استخدام Guard المحدد (api للـ API requests)
        $authGuard = Auth::guard($guard);
        
        if (!$authGuard->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        $user = $authGuard->user();

        if (!$user->is_active) {
            $authGuard->logout();
            throw ValidationException::withMessages([
                'email' => ['حسابك غير مفعل'],
            ]);
        }

        $user->updateLastLogin();
        
        // إنشاء token فقط للـ API guard
        if ($guard === 'api') {
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token,
            ];
        }
        
        // للـ web guard (Dashboard) لا نحتاج token
        return [
            'user' => $user,
        ];
    }

    public function logout(User $user): bool
    {
        $user->currentAccessToken()->delete();
        return true;
    }
}