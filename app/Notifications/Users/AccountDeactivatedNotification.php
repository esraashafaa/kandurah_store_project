<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
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
            ->subject('⚠️ تم إلغاء تفعيل حسابك')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('نأسف لإبلاغك بأن حسابك قد تم إلغاء تفعيله.');

        if ($this->reason) {
            $mail->line('**السبب:** ' . $this->reason);
        }

        $mail->line('إذا كان لديك أي استفسارات، يرجى التواصل معنا.')
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
            'title' => 'تم إلغاء تفعيل حسابك',
            'message' => 'تم إلغاء تفعيل حسابك.' . ($this->reason ? ' السبب: ' . $this->reason : ''),
            'reason' => $this->reason,
            'icon' => '⚠️',
            'type' => 'account_deactivated',
            'action_url' => route('contact'),
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
            'reason' => $this->reason,
        ];
    }
}
