<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
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
            ->subject('✅ تم تفعيل حسابك')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تفعيل حسابك بنجاح.')
            ->line('يمكنك الآن استخدام جميع ميزات المنصة.')
            ->action('تسجيل الدخول', route('login'))
            ->line('شكراً لاستخدامك منصتنا!')
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
            'title' => 'تم تفعيل حسابك',
            'message' => 'تم تفعيل حسابك بنجاح. يمكنك الآن استخدام جميع الميزات.',
            'icon' => '✅',
            'type' => 'account_activated',
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
        return [];
    }

    /**
     * Get the Firebase representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => 'تم تفعيل حسابك',
            'body' => 'تم تفعيل حسابك بنجاح. يمكنك الآن استخدام جميع الميزات.',
            'data' => [
                'type' => 'account_activated',
                'action_url' => route('dashboard'),
            ],
        ];
    }
}
