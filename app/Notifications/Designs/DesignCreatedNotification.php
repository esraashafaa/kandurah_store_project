<?php

namespace App\Notifications\Designs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للأدمن عند إنشاء تصميم جديد
 */
class DesignCreatedNotification extends Notification
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
        $channels = ['database'];
        if (!empty($notifiable->fcm_token ?? null)) {
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
            ->subject('🎨 تصميم جديد - ' . $this->design->getTranslation('name', 'ar'))
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم إنشاء تصميم جديد في المنصة.')
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
            'title' => 'New Design Created',
            'message' => 'A new design has been created by a user. Tap to review it in the design list',
            'design_id' => $this->design->id,
            'design_name' => $this->design->getTranslation('name', 'ar'),
            'icon' => '🎨',
            'type' => 'design_created',
            'action_url' => route('dashboard.designs.index'),
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
            'title' => 'New Design Created',
            'body' => 'A new design has been created by a user. Tap to review it in the design list',
            'data' => [
                'type' => 'design_created',
                'design_id' => $this->design->id,
                'design_name' => $this->design->getTranslation('name', 'ar'),
                'action_url' => route('dashboard.designs.index'),
            ],
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
