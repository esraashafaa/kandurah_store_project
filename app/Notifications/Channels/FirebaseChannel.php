<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class FirebaseChannel
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // التحقق من وجود FCM Token
        if (!$notifiable->fcm_token) {
            Log::debug('User has no FCM token', [
                'user_id' => $notifiable->id,
                'notification_type' => get_class($notification),
            ]);
            return;
        }

        // الحصول على بيانات الإشعار
        if (!method_exists($notification, 'toFirebase')) {
            Log::debug('Notification does not implement toFirebase method', [
                'notification_type' => get_class($notification),
            ]);
            return;
        }

        $firebaseData = $notification->toFirebase($notifiable);

        if (!$firebaseData) {
            return;
        }

        $title = $firebaseData['title'] ?? 'إشعار جديد';
        $body = $firebaseData['body'] ?? '';
        $data = $firebaseData['data'] ?? [];

        // إرسال الإشعار
        $this->firebaseService->sendToUser(
            $notifiable->fcm_token,
            $title,
            $body,
            $data
        );
    }
}
