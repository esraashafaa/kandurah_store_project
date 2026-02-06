<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // إضافة Firebase Channel إذا كان المستخدم لديه FCM Token
        if ($notifiable->fcm_token) {
            $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
        }
        
        Log::info('OrderCreatedNotification via() called', [
            'order_id' => $this->order->id,
            'user_id' => $notifiable->id,
            'channels' => $channels,
            'has_fcm_token' => !empty($notifiable->fcm_token),
        ]);
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ تم إنشاء طلبك بنجاح - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إنشاء طلبك بنجاح.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**المبلغ الإجمالي:** $' . number_format($this->order->total_amount, 2))
            ->line('**الحالة:** ' . $this->order->status->label())
            ->action('عرض تفاصيل الطلب', route('dashboard.orders.show', $this->order->id))
            ->line('شكراً لاستخدامك منصتنا!')
            ->salutation('مع أطيب التحيات، فريق ' . config('app.name'));
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $data = [
            'title' => 'New Order Created',
            'message' => 'Your order #' . $this->order->id . ' has been created successfully. Tap to view the order details',
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status->value,
            'icon' => '🛒',
            'type' => 'order_created',
            'action_url' => route('dashboard.orders.show', $this->order->id),
        ];
        
        // التحقق من أن البيانات قابلة للـ JSON encoding
        $jsonData = json_encode($data);
        if ($jsonData === false) {
            Log::error('OrderCreatedNotification toDatabase - JSON encoding failed', [
                'order_id' => $this->order->id,
                'user_id' => $notifiable->id,
                'json_error' => json_last_error_msg(),
                'data' => $data,
            ]);
        }
        
        Log::info('OrderCreatedNotification toDatabase called', [
            'order_id' => $this->order->id,
            'user_id' => $notifiable->id,
            'data' => $data,
            'json_valid' => $jsonData !== false,
            'json_length' => $jsonData ? strlen($jsonData) : 0,
        ]);
        
        return $data;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status->value,
        ];
    }

    /**
     * Get the Firebase representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => 'New Order Created',
            'body' => 'Your order #' . $this->order->id . ' has been created successfully. Tap to view the order details',
            'data' => [
                'type' => 'order_created',
                'order_id' => $this->order->id,
                'total_amount' => (string) $this->order->total_amount,
                'status' => $this->order->status->value,
                'action_url' => route('dashboard.orders.show', $this->order->id),
            ],
        ];
    }
}
