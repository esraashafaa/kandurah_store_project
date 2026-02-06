<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderStatusChanged;
use App\Events\Orders\OrderCancelled;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        Log::info('Order created - dispatching OrderCreated event', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ]);
        
        // تفعيل حدث إنشاء الطلب
        Event::dispatch(new OrderCreated($order));
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // التحقق من تغيير الحالة
        if ($order->wasChanged('status')) {
            Log::info('Order status changed detected in Observer', [
                'order_id' => $order->id,
                'changed_attributes' => $order->getChanges(),
            ]);
            // الحصول على الحالة القديمة من getOriginal
            // getOriginal يعيد القيمة الأصلية قبل التحديث (قبل cast)
            $oldStatusValue = $order->getOriginal('status');
            
            // التحقق من النوع وتحويله إذا لزم الأمر
            if ($oldStatusValue instanceof OrderStatus) {
                $oldStatus = $oldStatusValue;
            } elseif (is_string($oldStatusValue)) {
                $oldStatus = OrderStatus::from($oldStatusValue);
            } else {
                // إذا لم نستطع الحصول على الحالة القديمة، نحاول من getChanges
                $changes = $order->getChanges();
                if (isset($changes['status'])) {
                    // الحالة القديمة موجودة في getOriginal
                    $oldStatus = OrderStatus::from($order->getOriginal('status'));
                } else {
                    // كحل أخير، نستخدم الحالة الحالية (يجب ألا يحدث هذا)
                    Log::warning('Could not determine old status in OrderObserver', [
                        'order_id' => $order->id,
                        'original_status' => $oldStatusValue,
                    ]);
                    return;
                }
            }
            
            $newStatus = $order->status;

            Log::info('Dispatching OrderStatusChanged event', [
                'order_id' => $order->id,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
            ]);

            // تفعيل حدث تغيير الحالة
            Event::dispatch(new OrderStatusChanged($order, $oldStatus, $newStatus));

            // إذا كانت الحالة الجديدة "ملغي"، تفعيل حدث الإلغاء
            if ($newStatus === OrderStatus::CANCELLED) {
                Event::dispatch(new OrderCancelled($order));
            }
        }
    }
}
