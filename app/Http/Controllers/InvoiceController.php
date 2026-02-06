<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * عرض الفاتورة
     */
    public function show(Order $order)
    {
        // التحقق من وجود فاتورة
        if (!$order->invoice) {
            abort(404, 'الفاتورة غير موجودة');
        }

        // التحقق من تسجيل الدخول
        $user = auth()->user() ?? auth()->guard('admin')->user();
        if (!$user) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من الصلاحيات (المستخدم يمكنه رؤية فواتيره فقط، والأدمن يمكنه رؤية الجميع)
        if ($user->cannot('view', $order)) {
            abort(403, 'غير مصرح لك بعرض هذه الفاتورة');
        }

        $invoice = $order->invoice;
        $order->load(['user', 'items.design', 'items.size', 'location', 'coupon']);

        return view('invoices.show', [
            'order' => $order,
            'invoice' => $invoice,
        ]);
    }

    /**
     * تحميل PDF للفاتورة
     */
    public function download(Order $order)
    {
        // التحقق من تسجيل الدخول
        $user = auth()->user() ?? auth()->guard('admin')->user();
        if (!$user) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من الصلاحيات
        if ($user->cannot('view', $order)) {
            abort(403, 'غير مصرح لك بتحميل هذه الفاتورة');
        }

        // إنشاء الفاتورة تلقائياً إذا لم تكن موجودة
        if (!$order->invoice) {
            $invoice = $this->createInvoice($order);
        } else {
            $invoice = $order->invoice;
        }

        // تحميل البيانات المطلوبة
        $order->load(['user', 'items.design', 'items.size', 'location', 'coupon']);

        // تعيين اللغة إلى الإنجليزية دائماً
        app()->setLocale('en');

        // إنشاء PDF مباشرة (دائماً PDF) مع إعدادات UTF-8
        $pdf = Pdf::loadView('invoices.invoice', [
            'order' => $order,
            'invoiceNumber' => $invoice->invoice_number,
        ])->setPaper('a4', 'portrait')
          ->setOption('enable-local-file-access', true)
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', false);

        // تحديث ملف PDF في storage
        $fileName = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());
        $invoice->update(['pdf_url' => $fileName]);

        // إرجاع PDF للتحميل
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * عرض PDF في المتصفح (دائماً PDF وبالإنجليزية)
     */
    public function view(Order $order)
    {
        // التحقق من تسجيل الدخول
        $user = auth()->user() ?? auth()->guard('admin')->user();
        if (!$user) {
            abort(401, 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من الصلاحيات
        if ($user->cannot('view', $order)) {
            abort(403, 'غير مصرح لك بعرض هذه الفاتورة');
        }

        // إنشاء الفاتورة تلقائياً إذا لم تكن موجودة
        if (!$order->invoice) {
            $invoice = $this->createInvoice($order);
        } else {
            $invoice = $order->invoice;
        }

        // تحميل البيانات المطلوبة
        $order->load(['user', 'items.design', 'items.size', 'location', 'coupon']);

        // تعيين اللغة إلى الإنجليزية دائماً
        app()->setLocale('en');

        // إنشاء PDF مباشرة (دائماً PDF) مع إعدادات UTF-8
        $pdf = Pdf::loadView('invoices.invoice', [
            'order' => $order,
            'invoiceNumber' => $invoice->invoice_number,
        ])->setPaper('a4', 'portrait')
          ->setOption('enable-local-file-access', true)
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', false);

        // تحديث ملف PDF في storage
        $fileName = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());
        $invoice->update(['pdf_url' => $fileName]);

        // إرجاع PDF مباشرة
        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * إنشاء فاتورة تلقائياً للطلب (دائماً PDF وبالإنجليزية)
     */
    private function createInvoice(Order $order): Invoice
    {
        // إنشاء رقم فاتورة فريد
        $invoiceNumber = $this->generateInvoiceNumber($order);

        // تحميل البيانات المطلوبة
        $order->load(['user', 'items.design', 'items.size', 'location', 'coupon']);

        // تعيين اللغة إلى الإنجليزية دائماً
        app()->setLocale('en');

        // إنشاء PDF للفاتورة مع إعدادات UTF-8
        $pdf = Pdf::loadView('invoices.invoice', [
            'order' => $order,
            'invoiceNumber' => $invoiceNumber,
        ])->setPaper('a4', 'portrait')
          ->setOption('enable-local-file-access', true)
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', false);

        // حفظ PDF في storage
        $fileName = "invoices/{$invoiceNumber}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());

        // إنشاء الفاتورة في قاعدة البيانات
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'pdf_url' => $fileName,
        ]);

        return $invoice;
    }

    /**
     * إنشاء رقم فاتورة فريد
     */
    private function generateInvoiceNumber(Order $order): string
    {
        $year = date('Y');
        $orderId = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $timestamp = now()->format('His');
        
        return "INV-{$year}-{$orderId}-{$timestamp}";
    }
}
