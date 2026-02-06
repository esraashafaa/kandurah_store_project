<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FCMController extends Controller
{
    /**
     * حفظ FCM Token للمستخدم
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            Log::info('FCM token saved', [
                'user_id' => $user->id,
                'token_preview' => substr($request->fcm_token, 0, 20) . '...',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ FCM Token بنجاح',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to save FCM token', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ FCM Token',
            ], 500);
        }
    }

    /**
     * حذف FCM Token للمستخدم
     */
    public function destroy(Request $request)
    {
        try {
            $user = $request->user();
            $user->fcm_token = null;
            $user->save();

            Log::info('FCM token removed', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف FCM Token بنجاح',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to remove FCM token', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف FCM Token',
            ], 500);
        }
    }
}
