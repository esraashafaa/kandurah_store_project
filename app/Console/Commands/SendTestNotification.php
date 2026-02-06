<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    protected $signature = 'notification:test 
                            {--user= : معرّف المستخدم أو البريد الإلكتروني (اختياري، إن لم يُحدد يُرسل لأول مستخدم)}
                            {--sync : إرسال فوري دون استخدام الطابور}';

    protected $description = 'إرسال إشعار تجريبي لمستخدم (للتجربة)';

    public function handle(): int
    {
        $userInput = $this->option('user');
        $useSync = $this->option('sync');

        $user = $userInput
            ? (is_numeric($userInput)
                ? User::find($userInput)
                : User::where('email', $userInput)->first())
            : User::where('is_active', true)->first();

        if (! $user) {
            $this->error('لم يتم العثور على مستخدم. أضف مستخدمين أولاً أو حدد --user=id أو --user=email');
            return self::FAILURE;
        }

        $title = 'إشعار تجريبي - ' . now()->format('H:i');
        $message = 'هذا إشعار تجريبي من النظام. إذا وصلك فهذا يعني أن الإشعارات تعمل بشكل صحيح.';
        $type = 'system';

        $notification = new GenericNotification($title, $message, $type);

        if ($useSync) {
            $user->notifyNow($notification);
            $this->info('تم إرسال الإشعار فوراً (بدون طابور) إلى: ' . $user->email);
        } else {
            $user->notify($notification);
            $this->info('تم وضع الإشعار في الطابور للمستخدم: ' . $user->email);
            $this->comment('شغّل: php artisan queue:work لمعالجة الطابور وإرسال البريد/FCM.');
        }

        $this->newLine();
        $this->line('قنوات الإرسال: قاعدة البيانات + البريد' . ($user->fcm_token ? ' + Firebase' : ''));
        return self::SUCCESS;
    }
}
