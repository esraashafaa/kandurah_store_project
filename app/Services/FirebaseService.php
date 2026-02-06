<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.firebase.credentials_path');
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);

            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    /**
     * إرسال إشعار Push لمستخدم واحد
     */
    public function sendToUser(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (!$this->messaging || !$fcmToken) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            
            Log::info('Firebase notification sent successfully', [
                'fcm_token' => substr($fcmToken, 0, 20) . '...',
                'title' => $title,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: ' . $e->getMessage(), [
                'fcm_token' => substr($fcmToken, 0, 20) . '...',
                'title' => $title,
            ]);
            return false;
        }
    }

    /**
     * إرسال إشعار Push لعدة مستخدمين
     */
    public function sendToMultipleUsers(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($fcmTokens as $token) {
            if ($this->sendToUser($token, $title, $body, $data)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * إرسال إشعار Push لجميع المستخدمين النشطين
     */
    public function sendToAllActiveUsers(string $title, string $body, array $data = []): array
    {
        $tokens = \App\Models\User::whereNotNull('fcm_token')
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        return $this->sendToMultipleUsers($tokens, $title, $body, $data);
    }

    /**
     * التحقق من صحة FCM Token
     */
    public function validateToken(string $fcmToken): bool
    {
        if (!$this->messaging || !$fcmToken) {
            return false;
        }

        try {
            // محاولة إرسال رسالة فارغة للتحقق من صحة الـ Token
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(Notification::create('Test', 'Test'));
            
            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            // إذا كان الخطأ بسبب Token غير صالح، نعيد false
            if (str_contains($e->getMessage(), 'invalid') || str_contains($e->getMessage(), 'registration-token-not-registered')) {
                return false;
            }
            // أخطاء أخرى قد تكون مؤقتة
            Log::warning('Firebase token validation error: ' . $e->getMessage());
            return false;
        }
    }
}
