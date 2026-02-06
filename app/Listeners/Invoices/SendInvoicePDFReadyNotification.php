<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoicePDFReady;
use App\Notifications\Invoices\InvoicePDFReadyNotification;

class SendInvoicePDFReadyNotification
{
    /**
     * Handle the event.
     */
    public function handle(InvoicePDFReady $event): void
    {
        $event->order->user->notify(new InvoicePDFReadyNotification(
            $event->order,
            $event->pdfPath
        ));
    }
}
