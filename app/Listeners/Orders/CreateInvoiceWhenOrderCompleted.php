<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderStatusChanged;
use App\Models\Invoice;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateInvoiceWhenOrderCompleted
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;

        // التحقق من أن الحالة الجديدة هي DELIVERED (مكتمل)
        if ($newStatus !== OrderStatus::DELIVERED) {
            return;
        }

        // التحقق من عدم وجود فاتورة مسبقاً
        if ($order->invoice) {
            Log::info('Invoice already exists for order', [
                'order_id' => $order->id,
                'invoice_id' => $order->invoice->id,
            ]);
            return;
        }

        try {
            DB::transaction(function () use ($order) {
                // 1. إنشاء رقم فاتورة فريد
                $invoiceNumber = $this->generateInvoiceNumber($order);

                // 2. إنشاء PDF للفاتورة
                $pdfPath = $this->generateInvoicePDF($order, $invoiceNumber);

                // 3. إنشاء الفاتورة في قاعدة البيانات
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'pdf_url' => $pdfPath,
                ]);

                Log::info('Invoice created successfully', [
                    'order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoiceNumber,
                    'pdf_url' => $pdfPath,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create invoice for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * إنشاء رقم فاتورة فريد
     */
    private function generateInvoiceNumber($order): string
    {
        $year = date('Y');
        $orderId = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $timestamp = now()->format('His');
        
        return "INV-{$year}-{$orderId}-{$timestamp}";
    }

    /**
     * إنشاء PDF للفاتورة
     */
    private function generateInvoicePDF($order, string $invoiceNumber): string
    {
        // تحميل البيانات المطلوبة
        $order->load(['user', 'items.design', 'items.size', 'location', 'coupon']);

        // إنشاء PDF من View
        $pdf = Pdf::loadView('invoices.invoice', [
            'order' => $order,
            'invoiceNumber' => $invoiceNumber,
        ]);

        // حفظ PDF في storage
        $fileName = "invoices/{$invoiceNumber}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());

        // إرجاع المسار النسبي للوصول إلى الملف
        return $fileName;
    }
}
