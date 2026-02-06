<?php

namespace App\Notifications\Invoices;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePDFReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $pdfPath
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
            ->subject('📄 الفاتورة جاهزة للتحميل - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('الفواتير الخاصة بطلبك جاهزة الآن للتحميل.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->action('تحميل الفاتورة', route('dashboard.orders.invoice-pdf', $this->order->id))
            ->line('يمكنك تحميل نسخة PDF من الفاتورة من الرابط أعلاه.')
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
            'title' => 'الفاتورة جاهزة للتحميل',
            'message' => 'فاتورة طلبك رقم #' . $this->order->id . ' جاهزة للتحميل',
            'order_id' => $this->order->id,
            'pdf_path' => $this->pdfPath,
            'icon' => '📄',
            'type' => 'invoice_pdf_ready',
            'action_url' => route('dashboard.orders.invoice-pdf', $this->order->id),
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
            'pdf_path' => $this->pdfPath,
        ];
    }
}
