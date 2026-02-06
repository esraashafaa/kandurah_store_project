<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public OrderStatus $oldStatus,
        public OrderStatus $newStatus
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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $icon = match($this->newStatus) {
            OrderStatus::CONFIRMED => '✅',
            OrderStatus::PROCESSING => '⚙️',
            OrderStatus::SHIPPED => '🚚',
            OrderStatus::DELIVERED => '🎉',
            OrderStatus::CANCELLED => '❌',
            default => '📦',
        };

        return (new MailMessage)
            ->subject($icon . ' تم تحديث حالة طلبك - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تحديث حالة طلبك.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**الحالة السابقة:** ' . $this->oldStatus->label())
            ->line('**الحالة الجديدة:** ' . $this->newStatus->label())
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
        $icon = match($this->newStatus) {
            OrderStatus::CONFIRMED => '✅',
            OrderStatus::PROCESSING => '⚙️',
            OrderStatus::SHIPPED => '🚚',
            OrderStatus::DELIVERED => '🎉',
            OrderStatus::CANCELLED => '❌',
            default => '📦',
        };

        return [
            'title' => 'Order Status Updated',
            'message' => 'The status of your order #' . $this->order->id . ' has been updated to ' . $this->newStatus->label() . '. Tap to view details',
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'icon' => $icon,
            'type' => 'order_status_changed',
            'action_url' => route('dashboard.orders.show', $this->order->id),
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
            'title' => 'Order Status Updated',
            'body' => 'The status of your order #' . $this->order->id . ' has been updated to ' . $this->newStatus->label() . '. Tap to view details',
            'data' => [
                'type' => 'order_status_changed',
                'order_id' => $this->order->id,
                'old_status' => $this->oldStatus->value,
                'new_status' => $this->newStatus->value,
                'action_url' => route('dashboard.orders.show', $this->order->id),
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
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
        ];
    }
}
