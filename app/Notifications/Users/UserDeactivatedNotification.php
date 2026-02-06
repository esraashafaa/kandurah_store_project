<?php

namespace App\Notifications\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للأدمن عند إلغاء تفعيل مستخدم
 */
class UserDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $deactivatedUser,
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
            ->subject('⚠️ تم إلغاء تفعيل مستخدم - ' . $this->deactivatedUser->name)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إلغاء تفعيل حساب مستخدم.')
            ->line('**الاسم:** ' . $this->deactivatedUser->name)
            ->line('**البريد الإلكتروني:** ' . $this->deactivatedUser->email);

        if ($this->reason) {
            $mail->line('**السبب:** ' . $this->reason);
        }

        $mail->action('عرض الملف الشخصي', route('dashboard.users.show', $this->deactivatedUser->id))
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
            'title' => 'تم إلغاء تفعيل مستخدم',
            'message' => 'تم إلغاء تفعيل حساب ' . $this->deactivatedUser->name . ($this->reason ? ' - ' . $this->reason : ''),
            'user_id' => $this->deactivatedUser->id,
            'user_name' => $this->deactivatedUser->name,
            'reason' => $this->reason,
            'icon' => '⚠️',
            'type' => 'user_deactivated',
            'action_url' => route('dashboard.users.show', $this->deactivatedUser->id),
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
            'user_id' => $this->deactivatedUser->id,
            'reason' => $this->reason,
        ];
    }
}
