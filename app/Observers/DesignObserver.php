<?php

namespace App\Observers;

use App\Models\Design;
use App\Events\Designs\DesignUpdated;
use Illuminate\Support\Facades\Event;

class DesignObserver
{
    /**
     * حدث "created" لا يطلق DesignCreated هنا — يُطلق مرة واحدة فقط من DesignService::createDesign()
     * لتجنب إرسال الإشعار مرتين.
     */
    public function created(Design $design): void
    {
        // لا شيء — DesignCreated يُرسل من DesignService بعد اكتمال الإنشاء
    }

    /**
     * Handle the Design "updated" event.
     */
    public function updated(Design $design): void
    {
        // تفعيل حدث تحديث التصميم (للأدمن فقط)
        Event::dispatch(new DesignUpdated($design));
    }
}
