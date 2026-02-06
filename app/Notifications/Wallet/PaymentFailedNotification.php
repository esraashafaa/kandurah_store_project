<?php

namespace App\Notifications\Wallet;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $reason,
        public string $paymentMethod
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
        $paymentMethodLabel = match($this->paymentMethod) {
            'card' => 'بطاقة الائتمان',
            'wallet' => 'المحفظة',
            default => $this->paymentMethod,
        };

        return (new MailMessage)
            ->subject('❌ فشل عملية الدفع - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('نأسف لإبلاغك بأن عملية الدفع قد فشلت.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**المبلغ:** $' . number_format($this->order->total_amount, 2))
            ->line('**طريقة الدفع:** ' . $paymentMethodLabel)
            ->line('**السبب:** ' . $this->reason)
            ->action('إعادة المحاولة', route('dashboard.orders.show', $this->order->id))
            ->line('يرجى التحقق من معلومات الدفع والمحاولة مرة أخرى.')
            ->line('إذا استمرت المشكلة، يرجى التواصل معنا.')
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
            'title' => 'فشل عملية الدفع',
            'message' => 'فشلت عملية الدفع لطلبك رقم #' . $this->order->id . ' - ' . $this->reason,
            'order_id' => $this->order->id,
            'reason' => $this->reason,
            'payment_method' => $this->paymentMethod,
            'icon' => '❌',
            'type' => 'payment_failed',
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
            'payment_method' => $this->paymentMethod,
        ];
    }
}
