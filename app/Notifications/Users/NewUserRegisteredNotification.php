<?php

namespace App\Notifications\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للأدمن عند تسجيل مستخدم جديد
 */
class NewUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $newUser
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
            ->subject('👤 مستخدم جديد مسجل - ' . $this->newUser->name)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تسجيل مستخدم جديد في المنصة.')
            ->line('**الاسم:** ' . $this->newUser->name)
            ->line('**البريد الإلكتروني:** ' . $this->newUser->email)
            ->line('**تاريخ التسجيل:** ' . $this->newUser->created_at->format('Y-m-d H:i'))
            ->action('عرض الملف الشخصي', route('dashboard.users.show', $this->newUser->id))
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
            'title' => 'مستخدم جديد',
            'message' => 'تم تسجيل مستخدم جديد: ' . $this->newUser->name,
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'icon' => '👤',
            'type' => 'new_user_registered',
            'action_url' => route('dashboard.users.show', $this->newUser->id),
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
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
        ];
    }
}
