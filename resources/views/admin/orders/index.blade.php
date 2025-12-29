@extends('layouts.admin')

@section('title', 'إدارة الطلبات')

@push('styles')
<style>
    .search-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .search-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إدارة الطلبات</h1>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع طلبات المتجر</p>
    </div>
    {{-- <div class="flex gap-2">
        <button onclick="exportOrders()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
            <i class="fas fa-file-excel"></i>
            <span>تصدير Excel</span>
        </button>
    </div> --}}
</div>

<!-- Statistics Cards -->
<div class="w-full flex gap-4 mb-8 flex-nowrap items-stretch">
    
    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-gray-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/5 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-shopping-cart text-gray-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">جميع الطلبات</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-yellow-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/5 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-yellow-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">قيد الانتظار</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-blue-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/5 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-spinner text-blue-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">قيد المعالجة</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['processing'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-green-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/5 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">مكتملة</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['completed'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-red-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/5 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-times-circle text-red-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">ملغاة</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['cancelled'] ?? 0 }}</p>
        </div>
    </div>

</div>

<!-- Filters & Search - Single Line -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="GET" action="{{ route('dashboard.orders.index') }}" class="flex flex-col lg:flex-row gap-4">
        
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ابحث برقم الطلب أو اسم العميل..."
                    class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >
                <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
            </div>
        </div>

        <!-- Status Filter -->
        <div class="w-full lg:w-48">
            <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>

        <!-- Payment Method -->
        <div class="w-full lg:w-48">
            <select name="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع طرق الدفع</option>
                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>نقداً</option>
                <option value="wallet" {{ request('payment_method') === 'wallet' ? 'selected' : '' }}>محفظة</option>
                <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>بطاقة</option>
            </select>
        </div>

        <!-- Date Range -->
        <div class="w-full lg:w-48">
            <input 
                type="date" 
                name="date_from" 
                value="{{ request('date_from') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
            >
        </div>

        <div class="flex gap-2">
            <button type="submit" class="search-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                <i class="fas fa-filter"></i>
                <span class="hidden sm:inline">بحث</span>
            </button>
            <a href="{{ route('dashboard.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
                <i class="fas fa-redo"></i>
                <span class="hidden sm:inline">إعادة تعيين</span>
            </a>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">رقم الطلب</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">العميل</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المبلغ</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">طريقة الدفع</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center ring-2 ring-gray-200">
                                <i class="fas fa-receipt text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">#{{ $order->id }}</p>
                                <p class="text-xs text-gray-500">{{ $order->items_count ?? 0 }} عنصر</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($order->user->avatar)
                                <img src="{{ Storage::disk('public')->url($order->user->avatar) }}" alt="{{ $order->user->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                    {{ substr($order->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-gray-400 text-sm"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($order->total_amount, 2) }} ر.س</p>
                                <p class="text-xs text-gray-500">{{ $order->items_count ?? 0 }} عنصر</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                            @if($order->payment_method === 'cash') bg-green-100 text-green-900
                            @elseif($order->payment_method === 'wallet') bg-purple-100 text-purple-900
                            @else bg-blue-100 text-blue-900
                            @endif
                        ">
                            <i class="fas 
                                @if($order->payment_method === 'cash') fa-money-bill
                                @elseif($order->payment_method === 'wallet') fa-wallet
                                @else fa-credit-card
                                @endif
                            "></i>
                            {{ __('orders.payment_methods.' . $order->payment_method) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select 
                            class="px-3 py-1.5 rounded-full text-xs font-semibold border-0 cursor-pointer transition-all duration-200
                                @if($order->status->value === 'pending') bg-yellow-100 text-yellow-900
                                @elseif($order->status->value === 'confirmed') bg-indigo-100 text-indigo-900
                                @elseif($order->status->value === 'processing') bg-blue-100 text-blue-900
                                @elseif($order->status->value === 'delivered' || $order->status->value === 'completed') bg-green-100 text-green-900
                                @else bg-red-100 text-red-900
                                @endif
                            "
                            onchange="updateOrderStatus({{ $order->id }}, this.value)"
                        >
                            <option value="pending" {{ $order->status->value === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ $order->status->value === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="processing" {{ $order->status->value === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="shipped" {{ $order->status->value === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                            <option value="delivered" {{ $order->status->value === 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                            <option value="cancelled" {{ $order->status->value === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $order->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 hover:bg-blue-50 rounded-lg" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="printInvoice({{ $order->id }})" class="text-green-600 hover:text-green-900 transition-colors duration-200 p-2 hover:bg-green-50 rounded-lg" title="طباعة">
                                <i class="fas fa-print"></i>
                            </button>
                            @if($order->status->value !== 'delivered' && $order->status->value !== 'cancelled' && $order->status->value !== 'completed')
                            <button 
                                onclick="cancelOrder({{ $order->id }})" 
                                class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 hover:bg-red-50 rounded-lg"
                                title="إلغاء"
                            >
                                <i class="fas fa-times-circle" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-semibold mb-2 text-gray-700">لا توجد طلبات</p>
                            <p class="text-sm text-gray-500">لم يتم العثور على أي طلبات تطابق معايير البحث</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Ensure Font Awesome icons are always visible
(function() {
    const fixIcons = () => {
        document.querySelectorAll('i.fas, i.far, i.fal, i.fab').forEach(icon => {
            icon.style.setProperty('font-family', 'Font Awesome 6 Free', 'important');
            icon.style.setProperty('font-weight', '900', 'important');
            icon.style.setProperty('display', 'inline-block', 'important');
            icon.style.setProperty('visibility', 'visible', 'important');
            icon.style.setProperty('opacity', '1', 'important');
            icon.style.setProperty('font-style', 'normal', 'important');
        });
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixIcons);
    } else {
        fixIcons();
    }
    
    [100, 300, 500, 1000, 2000].forEach(delay => {
        setTimeout(fixIcons, delay);
    });
})();

function updateOrderStatus(orderId, status) {
    fetch(`/dashboard/orders/${orderId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'تم تحديث حالة الطلب بنجاح');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'حدث خطأ أثناء تحديث حالة الطلب');
        }
    })
    .catch(error => {
        showNotification('error', 'حدث خطأ أثناء تحديث حالة الطلب');
        console.error('Error:', error);
    });
}

function cancelOrder(orderId) {
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.')) {
        return;
    }

    fetch(`/dashboard/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'تم إلغاء الطلب بنجاح');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'حدث خطأ أثناء إلغاء الطلب');
        }
    })
    .catch(error => {
        showNotification('error', 'حدث خطأ أثناء إلغاء الطلب');
        console.error('Error:', error);
    });
}

function printInvoice(orderId) {
    window.open(`/dashboard/orders/${orderId}/invoice/pdf`, '_blank');
}

function exportOrders() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/dashboard/orders/export?${params.toString()}`;
}

function showNotification(type, message) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 transition-all duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush

