<?php

namespace App\Notifications\Admins;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار لـ Super Admin عند إنشاء حساب Admin جديد
 */
class AdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Admin $admin,
        public $createdBy = null
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
        $createdByName = $this->createdBy ? $this->createdBy->name : 'النظام';
        
        return (new MailMessage)
            ->subject('👤 تم إنشاء حساب Admin جديد')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إنشاء حساب Admin جديد في النظام.')
            ->line('**اسم Admin:** ' . $this->admin->name)
            ->line('**البريد الإلكتروني:** ' . $this->admin->email)
            ->line('**الدور:** ' . $this->admin->role->label())
            ->line('**تم الإنشاء بواسطة:** ' . $createdByName)
            ->action('عرض تفاصيل Admin', route('admin.admins.show', $this->admin->id))
            ->line('يرجى مراجعة الحساب والتحقق من الصلاحيات الممنوحة.')
            ->salutation('مع أطيب التحيات، فريق ' . config('app.name'));
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $createdByName = $this->createdBy ? $this->createdBy->name : 'النظام';
        
        return [
            'title' => 'تم إنشاء حساب Admin جديد',
            'message' => 'تم إنشاء حساب Admin جديد: ' . $this->admin->name . ' (' . $this->admin->email . ')',
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'admin_email' => $this->admin->email,
            'admin_role' => $this->admin->role->value,
            'created_by' => $this->createdBy?->id,
            'created_by_name' => $createdByName,
            'icon' => '👤',
            'type' => 'admin_created',
            'action_url' => route('admin.admins.show', $this->admin->id),
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
            'admin_email' => $this->admin->email,
            'created_by' => $this->createdBy?->id,
        ];
    }
}
