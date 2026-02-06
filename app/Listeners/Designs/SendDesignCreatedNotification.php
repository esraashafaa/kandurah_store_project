<?php

namespace App\Listeners\Designs;

use App\Events\Designs\DesignCreated;
use App\Notifications\Designs\DesignCreatedNotification;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendDesignCreatedNotification
{
    /** نافذة منع التكرار: لا نرسل إشعاراً ثانياً لنفس التصميم لنفس الأدمن خلال هذه الثواني */
    private const DUPLICATE_WINDOW_SECONDS = 120;

    /**
     * إرسال إشعار "تصميم جديد" تلقائياً للسوبر أدمن فقط عند إنشاء مستخدم لتصميم.
     * مع منع إرسال إشعار مكرر لنفس التصميم خلال نافذة زمنية قصيرة.
     */
    public function handle(DesignCreated $event): void
    {
        $design = $event->design;

        Log::info('SendDesignCreatedNotification received', [
            'design_id' => $design->id,
            'user_id' => $design->user_id,
        ]);

        $superAdmins = Admin::role('super-admin')->get()->unique('id')->values();

        Log::info('Found super admins for DesignCreatedNotification', [
            'design_id' => $design->id,
            'super_admins_count' => $superAdmins->count(),
        ]);

        $since = now()->subSeconds(self::DUPLICATE_WINDOW_SECONDS);
        $designIdJson = '"design_id":' . (int) $design->id;

        if ($superAdmins->isNotEmpty()) {
            foreach ($superAdmins as $admin) {
                $alreadySent = DB::table('notifications')
                    ->where('notifiable_type', get_class($admin))
                    ->where('notifiable_id', $admin->id)
                    ->where('created_at', '>=', $since)
                    ->where('data', 'like', '%' . $designIdJson . '%')
                    ->exists();

                if ($alreadySent) {
                    Log::info('Skipping duplicate DesignCreatedNotification', [
                        'design_id' => $design->id,
                        'admin_id' => $admin->id,
                    ]);
                    continue;
                }

                Log::info('Sending DesignCreatedNotification to super admin', [
                    'design_id' => $design->id,
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                ]);
                $admin->notify(new DesignCreatedNotification($design));
            }
        } else {
            Log::warning('No super admins found - cannot send DesignCreatedNotification', [
                'design_id' => $design->id,
            ]);
        }
    }
}
