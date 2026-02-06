<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public ?string $reason = null
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('❌ تم إلغاء طلبك - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('نأسف لإبلاغك بأن طلبك قد تم إلغاؤه.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**المبلغ:** $' . number_format($this->order->total_amount, 2));

        if ($this->reason) {
            $mail->line('**السبب:** ' . $this->reason);
        }

        $mail->action('عرض تفاصيل الطلب', route('dashboard.orders.show', $this->order->id))
            ->line('إذا كان لديك أي استفسارات، يرجى التواصل معنا.')
            ->salutation('مع أطيب التحيات، فريق ' . config('app.name'));

        return $mail;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تم إلغاء طلبك',
            'message' => 'تم إلغاء طلبك رقم #' . $this->order->id . ($this->reason ? ' - ' . $this->reason : ''),
            'order_id' => $this->order->id,
            'reason' => $this->reason,
            'icon' => '❌',
            'type' => 'order_cancelled',
            'action_url' => route('dashboard.orders.show', $this->order->id),
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
            'reason' => $this->reason,
        ];
    }
}
