<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // محاولة تسجيل الدخول كـ User (استخدام guard الجلسة وليس api)
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();
            
            // التحقق من أن الحساب نشط
            if (!$user->is_active) {
                Auth::guard('web')->logout();
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => ['حسابك غير مفعل'],
                ]);
            }

            // تحديث آخر وقت تسجيل دخول
            if (method_exists($user, 'updateLastLogin')) {
                $user->updateLastLogin();
            }

            RateLimiter::clear($this->throttleKey());
            return;
        }

        // محاولة تسجيل الدخول كـ Admin
        $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
        
        if ($admin && \Illuminate\Support\Facades\Hash::check($credentials['password'], $admin->password)) {
            // التحقق من أن الحساب نشط
            if (!$admin->is_active) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => ['حسابك غير مفعل'],
                ]);
            }

            // تسجيل دخول Admin باستخدام guard الخاص بهم
            Auth::guard('admin')->login($admin, $remember);
            
            // حفظ Admin ID في session لاستخدامه في views
            $this->session()->put('admin_id', $admin->id);
            $this->session()->put('admin_guard', 'admin');
            
            // تحديث آخر وقت تسجيل دخول
            $admin->updateLastLogin();

            RateLimiter::clear($this->throttleKey());
            return;
        }

        // فشل تسجيل الدخول
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
     public function messages(): array
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'The password field is required.',
        ];
    }
}
