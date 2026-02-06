<?php

namespace App\Notifications\Invoices;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $invoiceNumber
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
        return (new MailMessage)
            ->subject('🧾 تم إنشاء فاتورتك - رقم ' . $this->invoiceNumber)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إنشاء فاتورة لطلبك.')
            ->line('**رقم الفاتورة:** ' . $this->invoiceNumber)
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**المبلغ الإجمالي:** $' . number_format($this->order->total_amount, 2))
            ->action('عرض الفاتورة', route('dashboard.orders.show', $this->order->id))
            ->line('يمكنك تحميل نسخة PDF من الفاتورة من صفحة الطلب.')
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
            'title' => 'تم إنشاء فاتورتك',
            'message' => 'تم إنشاء فاتورة رقم ' . $this->invoiceNumber . ' لطلبك رقم #' . $this->order->id,
            'invoice_number' => $this->invoiceNumber,
            'order_id' => $this->order->id,
            'icon' => '🧾',
            'type' => 'invoice_generated',
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
            'invoice_number' => $this->invoiceNumber,
            'order_id' => $this->order->id,
        ];
    }
}
