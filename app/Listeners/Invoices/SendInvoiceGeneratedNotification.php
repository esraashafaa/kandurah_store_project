<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoiceGenerated;
use App\Notifications\Invoices\InvoiceGeneratedNotification;

class SendInvoiceGeneratedNotification
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceGenerated $event): void
    {
        $event->order->user->notify(new InvoiceGeneratedNotification(
            $event->order,
            $event->invoiceNumber
        ));
    }
}
