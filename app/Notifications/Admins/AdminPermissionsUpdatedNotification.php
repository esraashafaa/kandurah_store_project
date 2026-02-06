<?php

namespace App\Notifications\Admins;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار لـ Super Admin عند تحديث صلاحيات Admin
 */
class AdminPermissionsUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Admin $admin,
        public array $oldPermissions,
        public array $newPermissions,
        public ?Admin $updatedBy = null
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
        $updatedByName = $this->updatedBy ? $this->updatedBy->name : 'النظام';
        
        $addedPermissions = array_diff($this->newPermissions, $this->oldPermissions);
        $removedPermissions = array_diff($this->oldPermissions, $this->newPermissions);
        
        $mail = (new MailMessage)
            ->subject('🔐 تم تحديث صلاحيات Admin')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تحديث صلاحيات Admin في النظام.')
            ->line('**اسم Admin:** ' . $this->admin->name)
            ->line('**البريد الإلكتروني:** ' . $this->admin->email)
            ->line('**تم التحديث بواسطة:** ' . $updatedByName);

        if (!empty($addedPermissions)) {
            $mail->line('**الصلاحيات المضافة:**')
                ->line(implode(', ', $addedPermissions));
        }

        if (!empty($removedPermissions)) {
            $mail->line('**الصلاحيات المُلغاة:**')
                ->line(implode(', ', $removedPermissions));
        }

        $mail->action('عرض تفاصيل Admin', route('admin.admins.show', $this->admin->id))
            ->line('يرجى مراجعة التغييرات والتحقق من صحتها.')
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
        $updatedByName = $this->updatedBy ? $this->updatedBy->name : 'النظام';
        $addedPermissions = array_diff($this->newPermissions, $this->oldPermissions);
        $removedPermissions = array_diff($this->oldPermissions, $this->newPermissions);
        
        $message = 'تم تحديث صلاحيات Admin: ' . $this->admin->name;
        if (!empty($addedPermissions)) {
            $message .= ' - تمت إضافة ' . count($addedPermissions) . ' صلاحية';
        }
        if (!empty($removedPermissions)) {
            $message .= ' - تم إلغاء ' . count($removedPermissions) . ' صلاحية';
        }
        
        return [
            'title' => 'تم تحديث صلاحيات Admin',
            'message' => $message,
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'admin_email' => $this->admin->email,
            'old_permissions' => $this->oldPermissions,
            'new_permissions' => $this->newPermissions,
            'added_permissions' => array_values($addedPermissions),
            'removed_permissions' => array_values($removedPermissions),
            'updated_by' => $this->updatedBy?->id,
            'updated_by_name' => $updatedByName,
            'icon' => '🔐',
            'type' => 'admin_permissions_updated',
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
            'old_permissions' => $this->oldPermissions,
            'new_permissions' => $this->newPermissions,
            'updated_by' => $this->updatedBy?->id,
        ];
    }
}
