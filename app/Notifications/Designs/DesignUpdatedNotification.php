<?php

namespace App\Notifications\Designs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للأدمن عند تحديث تصميم
 */
class DesignUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Design $design
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
            ->subject('🔄 تم تحديث تصميم - ' . $this->design->getTranslation('name', 'ar'))
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تحديث تصميم في المنصة.')
            ->line('**الاسم:** ' . $this->design->getTranslation('name', 'ar'))
            ->line('**المصمم:** ' . $this->design->user->name)
            ->line('**السعر:** $' . number_format($this->design->price, 2))
            ->action('عرض التصميم', route('dashboard.designs.show', $this->design->id))
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
            'title' => 'تم تحديث تصميم',
            'message' => 'تم تحديث التصميم: ' . $this->design->getTranslation('name', 'ar') . ' من ' . $this->design->user->name,
            'design_id' => $this->design->id,
            'design_name' => $this->design->getTranslation('name', 'ar'),
            'designer_name' => $this->design->user->name,
            'icon' => '🔄',
            'type' => 'design_updated',
            'action_url' => route('dashboard.designs.show', $this->design->id),
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
            'design_id' => $this->design->id,
            'design_name' => $this->design->getTranslation('name', 'ar'),
        ];
    }
}
