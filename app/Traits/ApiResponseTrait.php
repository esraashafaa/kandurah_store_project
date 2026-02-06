<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Standard API response format per project specification:
 * { "success", "message", "data", "timestamp" }
 */
trait ApiResponseTrait
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Operation completed successfully',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }

    protected function errorResponse(
        string $message = 'An error occurred',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $code);
    }
}
