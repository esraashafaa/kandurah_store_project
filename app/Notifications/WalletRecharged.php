<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletRecharged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The amount that was recharged.
     */
    public $amount;

    /**
     * The transaction ID.
     */
    public $transactionId;

    /**
     * Create a new notification instance.
     */
    public function __construct($amount, $transactionId = null)
    {
        $this->amount = $amount;
        $this->transactionId = $transactionId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
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
            ->subject('✅ تم شحن محفظتك بنجاح')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم شحن محفظتك بنجاح.')
            ->line('**المبلغ المضاف:** $' . number_format($this->amount, 2))
            ->line('**رصيدك الحالي:** $' . number_format($notifiable->wallet_balance, 2))
            ->action('عرض محفظتي', route('dashboard'))
            ->line('شكراً لاستخدامك منصتنا!')
            ->line('إذا لم تقم بهذه العملية، يرجى التواصل معنا فوراً.')
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
            'title' => 'تم شحن محفظتك',
            'message' => 'تم إضافة $' . number_format($this->amount, 2) . ' إلى محفظتك',
            'amount' => $this->amount,
            'transaction_id' => $this->transactionId,
            'new_balance' => $notifiable->wallet_balance,
            'icon' => '💰',
            'type' => 'wallet_recharge',
            'action_url' => route('dashboard'),
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
            'amount' => $this->amount,
            'transaction_id' => $this->transactionId,
            'new_balance' => $notifiable->wallet_balance,
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
            'title' => 'تم شحن محفظتك',
            'body' => 'تم إضافة $' . number_format($this->amount, 2) . ' إلى محفظتك. الرصيد الحالي: $' . number_format($notifiable->wallet_balance, 2),
            'data' => [
                'type' => 'wallet_recharge',
                'amount' => (string) $this->amount,
                'transaction_id' => (string) $this->transactionId,
                'new_balance' => (string) $notifiable->wallet_balance,
                'action_url' => route('dashboard'),
            ],
        ];
    }
}
