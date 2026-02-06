<?php

namespace App\Notifications\Admins;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار لـ Super Admin عند إزالة حساب Admin
 */
class AdminRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Admin $admin,
        public $removedBy = null,
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
        $removedByName = $this->removedBy ? $this->removedBy->name : 'النظام';
        
        $mail = (new MailMessage)
            ->subject('🗑️ تم إزالة حساب Admin')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إزالة حساب Admin من النظام.')
            ->line('**اسم Admin:** ' . $this->admin->name)
            ->line('**البريد الإلكتروني:** ' . $this->admin->email)
            ->line('**الدور السابق:** ' . $this->admin->role->label())
            ->line('**تم الإزالة بواسطة:** ' . $removedByName);

        if ($this->reason) {
            $mail->line('**السبب:** ' . $this->reason);
        }

        $mail->line('يرجى التأكد من أن جميع الصلاحيات تم إلغاؤها بشكل صحيح.')
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
        $removedByName = $this->removedBy ? $this->removedBy->name : 'النظام';
        
        return [
            'title' => 'تم إزالة حساب Admin',
            'message' => 'تم إزالة حساب Admin: ' . $this->admin->name . ' (' . $this->admin->email . ')',
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'admin_email' => $this->admin->email,
            'admin_role' => $this->admin->role->value,
            'removed_by' => $this->removedBy?->id,
            'removed_by_name' => $removedByName,
            'reason' => $this->reason,
            'icon' => '🗑️',
            'type' => 'admin_removed',
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
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'removed_by' => $this->removedBy?->id,
            'reason' => $this->reason,
        ];
    }
}
