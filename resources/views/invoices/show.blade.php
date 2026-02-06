@extends('layouts.user')

@section('title', 'فاتورة #' . $invoice->invoice_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">فاتورة #{{ $invoice->invoice_number }}</h1>
                <p class="text-gray-600">رقم الطلب: #{{ $order->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard.orders.invoice.download', $order) }}" 
                   class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    تحميل PDF
                </a>
                <a href="{{ route('dashboard.orders.invoice.view', $order) }}" 
                   target="_blank"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    عرض PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الفاتورة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">رقم الفاتورة</p>
                <p class="text-lg font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ الفاتورة</p>
                <p class="text-lg font-semibold text-gray-900">{{ $invoice->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">المجموع الكلي</p>
                <p class="text-lg font-semibold text-indigo-600">{{ number_format($invoice->total_amount, 2) }} ر.س</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">حالة الطلب</p>
                <p class="text-lg font-semibold text-gray-900">{{ $order->status->label() }}</p>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">ملخص الطلب</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">العميل</p>
                <p class="text-lg font-semibold text-gray-900">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
            </div>
            @if($order->location)
            <div>
                <p class="text-sm text-gray-600">عنوان التوصيل</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $order->location->city }} - {{ $order->location->area }}
                </p>
                @if($order->location->detailed_address)
                <p class="text-sm text-gray-500">{{ $order->location->detailed_address }}</p>
                @endif
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600">عدد العناصر</p>
                <p class="text-lg font-semibold text-gray-900">{{ $order->items->count() }} عنصر</p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('dashboard.orders.show', $order) }}" 
           class="text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            العودة إلى تفاصيل الطلب
        </a>
    </div>
</div>
@endsection
