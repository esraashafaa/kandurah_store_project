<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للمصمم عند إنشاء طلب جديد لتصميمه
 */
class NewOrderNotification extends Notification
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
        
        if ($notifiable->fcm_token) {
            $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🛒 طلب جديد - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إنشاء طلب جديد.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**العميل:** ' . $this->order->user->name)
            ->line('**المبلغ الإجمالي:** $' . number_format($this->order->total_amount, 2))
            ->line('**الحالة:** ' . $this->order->status->label())
            ->action('عرض تفاصيل الطلب', route('dashboard.orders.show', $this->order->id))
            ->line('يرجى مراجعة الطلب والتحقق منه.')
            ->salutation('مع أطيب التحيات، فريق ' . config('app.name'));
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Order Created',
            'message' => 'A new order has been placed for your design. Tap to view the order details',
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'user_name' => $this->order->user->name,
            'total_amount' => $this->order->total_amount,
            'icon' => '🛒',
            'type' => 'new_order',
            'action_url' => route('dashboard.orders.index'),
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
            'body' => 'A new order has been placed for your design. Tap to view the order details',
            'data' => [
                'type' => 'new_order',
                'order_id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'total_amount' => (string) $this->order->total_amount,
                'action_url' => route('dashboard.orders.index'),
            ],
        ];
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
            'user_id' => $this->order->user_id,
            'total_amount' => $this->order->total_amount,
        ];
    }
}
