<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Registration completed successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated(), 'api');

        return $this->successResponse([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function logout()
    {
        $this->authService->logout(auth()->user());

        return $this->successResponse(null, 'Logged out successfully');
    }
}