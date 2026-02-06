<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderCreated;
use App\Notifications\Orders\OrderCreatedNotification;
use App\Notifications\Orders\NewOrderNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendOrderCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        Log::info('SendOrderCreatedNotification received', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ]);
        
        // تحميل items مع design و user إذا لم تكن محملة
        if (!$order->relationLoaded('items')) {
            $order->load('items.design.user');
        }

        // إرسال إشعار للمستخدم (صاحب الطلب)
        if ($order->user) {
            Log::info('Sending OrderCreatedNotification to user', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'user_name' => $order->user->name,
            ]);
            
            try {
                // الحصول على عدد الإشعارات قبل الإرسال
                $beforeCount = DB::table('notifications')
                    ->where('notifiable_id', $order->user_id)
                    ->where('notifiable_type', 'App\Models\User')
                    ->count();
                
                // إرسال الإشعار
                $order->user->notify(new OrderCreatedNotification($order));
                
                // الانتظار قليلاً للتأكد من الحفظ
                usleep(100000); // 0.1 ثانية
                
                // التحقق من أن الإشعار تم حفظه في قاعدة البيانات
                $afterCount = DB::table('notifications')
                    ->where('notifiable_id', $order->user_id)
                    ->where('notifiable_type', 'App\Models\User')
                    ->count();
                
                $newNotifications = DB::table('notifications')
                    ->where('notifiable_id', $order->user_id)
                    ->where('notifiable_type', 'App\Models\User')
                    ->where('type', 'App\Notifications\Orders\OrderCreatedNotification')
                    ->where('created_at', '>=', now()->subMinute())
                    ->get();
                
                Log::info('OrderCreatedNotification sent to user successfully', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'notifications_before' => $beforeCount,
                    'notifications_after' => $afterCount,
                    'new_notifications_count' => $newNotifications->count(),
                    'new_notifications' => $newNotifications->map(function($n) {
                        return [
                            'id' => $n->id,
                            'type' => $n->type,
                            'created_at' => $n->created_at,
                        ];
                    })->toArray(),
                ]);
                
                if ($newNotifications->count() === 0) {
                    Log::error('OrderCreatedNotification was NOT saved to database!', [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'before_count' => $beforeCount,
                        'after_count' => $afterCount,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending OrderCreatedNotification', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } else {
            Log::warning('Order has no user - cannot send OrderCreatedNotification', [
                'order_id' => $order->id,
            ]);
        }

        // إرسال إشعار لمصممي التصاميم في الطلب
        $designOwners = collect();
        
        foreach ($order->items as $item) {
            if ($item->design && $item->design->user) {
                $designOwner = $item->design->user;
                // تجنب إرسال إشعار مكرر لنفس المصمم إذا كان لديه عدة تصاميم في الطلب
                if (!$designOwners->contains('id', $designOwner->id)) {
                    $designOwners->push($designOwner);
                    
                    Log::info('Sending NewOrderNotification to designer', [
                        'order_id' => $order->id,
                        'designer_id' => $designOwner->id,
                        'designer_name' => $designOwner->name,
                        'design_id' => $item->design->id,
                    ]);
                    
                    $designOwner->notify(new NewOrderNotification($order));
                    
                    Log::info('NewOrderNotification sent to designer successfully', [
                        'order_id' => $order->id,
                        'designer_id' => $designOwner->id,
                    ]);
                }
            }
        }
        
        if ($designOwners->isEmpty()) {
            Log::info('No designers found for order items - no NewOrderNotification sent', [
                'order_id' => $order->id,
                'items_count' => $order->items->count(),
            ]);
        }
    }
}
