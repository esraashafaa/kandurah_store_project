<?php

namespace App\Notifications\Wallet;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletDeductedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public float $amount,
        public Transaction $transaction,
        public ?string $description = null
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
            ->subject('💳 تم خصم من محفظتك')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم خصم مبلغ من محفظتك.')
            ->line('**المبلغ المخصوم:** $' . number_format($this->amount, 2))
            ->line('**رصيدك الحالي:** $' . number_format($notifiable->wallet_balance, 2))
            ->line('**رقم المعاملة:** #' . $this->transaction->id)
            ->when($this->description, fn($mail) => $mail->line('**الوصف:** ' . $this->description))
            ->action('عرض محفظتي', route('dashboard'))
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
            'title' => 'تم خصم من محفظتك',
            'message' => 'تم خصم $' . number_format($this->amount, 2) . ' من محفظتك',
            'amount' => $this->amount,
            'transaction_id' => $this->transaction->id,
            'new_balance' => $notifiable->wallet_balance,
            'description' => $this->description,
            'icon' => '💳',
            'type' => 'wallet_deducted',
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
            'transaction_id' => $this->transaction->id,
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
            'title' => 'تم خصم من محفظتك',
            'body' => 'تم خصم $' . number_format($this->amount, 2) . ' من محفظتك. الرصيد الحالي: $' . number_format($notifiable->wallet_balance, 2),
            'data' => [
                'type' => 'wallet_deducted',
                'amount' => (string) $this->amount,
                'transaction_id' => (string) $this->transaction->id,
                'new_balance' => (string) $notifiable->wallet_balance,
                'description' => $this->description,
                'action_url' => route('dashboard'),
            ],
        ];
    }
}
