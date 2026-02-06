<?php

namespace App\Notifications\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للأدمن عند وجود مشكلة في الطلب (مثل: فشل الدفع)
 */
class OrderIssueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $issueType,
        public string $message
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
            ->subject('⚠️ مشكلة في الطلب - طلب #' . $this->order->id)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('هناك مشكلة تتطلب انتباهك في أحد الطلبات.')
            ->line('**رقم الطلب:** #' . $this->order->id)
            ->line('**نوع المشكلة:** ' . $this->getIssueTypeLabel())
            ->line('**التفاصيل:** ' . $this->message)
            ->action('عرض تفاصيل الطلب', route('dashboard.orders.show', $this->order->id))
            ->line('يرجى مراجعة الطلب وحل المشكلة في أقرب وقت ممكن.')
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
            'title' => 'مشكلة في الطلب',
            'message' => $this->getIssueTypeLabel() . ': ' . $this->message,
            'order_id' => $this->order->id,
            'issue_type' => $this->issueType,
            'icon' => '⚠️',
            'type' => 'order_issue',
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
            'issue_type' => $this->issueType,
            'message' => $this->message,
        ];
    }

    /**
     * Get label for issue type
     */
    private function getIssueTypeLabel(): string
    {
        return match($this->issueType) {
            'payment_failed' => 'فشل الدفع',
            'inventory_issue' => 'مشكلة في المخزون',
            'shipping_issue' => 'مشكلة في الشحن',
            default => 'مشكلة غير محددة',
        };
    }
}
