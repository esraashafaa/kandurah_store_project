<?php

namespace App\Notifications\Traits;

trait HasFirebaseChannel
{
    /**
     * Add Firebase channel to notification channels if user has FCM token
     */
    protected function addFirebaseChannel(array $channels, $notifiable): array
    {
        if ($notifiable->fcm_token) {
            $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
        }
        
        return $channels;
    }
}
