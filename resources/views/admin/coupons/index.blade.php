@extends('layouts.admin')

@section('title', __('coupons.title'))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('coupons.title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('coupons.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>{{ __('coupons.add_new') }}</span>
    </a>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('coupons.stats.total') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-tags text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('coupons.stats.active') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('coupons.stats.expired') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['expired'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('coupons.stats.used') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['used'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-percentage text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Coupons Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($coupons as $coupon)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
        <!-- Coupon Header -->
        <div class="bg-gradient-to-r {{ $coupon->is_active ? 'from-green-50 to-green-100 border-r-4 border-green-500' : 'from-gray-50 to-gray-100 border-r-4 border-gray-400' }} p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-12 h-12 {{ $coupon->is_active ? 'bg-green-500' : 'bg-gray-400' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-tag text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('coupons.card.code_label') }}</p>
                        <p class="text-2xl font-bold tracking-wider text-gray-900">{{ $coupon->code }}</p>
                    </div>
                </div>
                <div class="text-left">
                    @if($coupon->is_active)
                        <span class="inline-block px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">
                            <i class="fas fa-check-circle ml-1"></i>
                            {{ __('coupons.card.active') }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-gray-400 text-white rounded-full text-xs font-semibold">
                            <i class="fas fa-times-circle ml-1"></i>
                            {{ __('coupons.card.inactive') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold {{ $coupon->is_active ? 'text-green-600' : 'text-gray-600' }}">
                        @if(($coupon->discount_type ?? 'percentage') === 'fixed')
                            {{ number_format($coupon->discount, 2) }} {{ __('coupons.currency') }}
                        @else
                            {{ number_format($coupon->discount, 2) }}%
                        @endif
                    </p>
                    <p class="text-sm text-gray-600">
                        @if(($coupon->discount_type ?? 'percentage') === 'fixed')
                            {{ __('coupons.card.discount_amount') }}
                        @else
                            {{ __('coupons.card.discount_percentage') }}
                        @endif
                    </p>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-bold text-gray-900">{{ $coupon->usage_count ?? 0 }}</p>
                    <p class="text-sm text-gray-600">{{ __('coupons.times_used') }}</p>
                </div>
            </div>
        </div>

        <!-- Coupon Body -->
        <div class="p-6">
            <div class="space-y-3 mb-4">
                @if($coupon->max_usage)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ __('coupons.card.max_usage') }}</span>
                    <span class="font-semibold text-gray-900">{{ $coupon->max_usage }} {{ __('coupons.times') }}</span>
                </div>
                @endif
                
                @if($coupon->min_purchase)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ __('coupons.card.min_purchase') }}</span>
                    <span class="font-semibold text-gray-900">{{ number_format($coupon->min_purchase, 2) }} {{ __('coupons.currency') }}</span>
                </div>
                @endif

                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ __('coupons.card.expires_at') }}</span>
                    <span class="font-semibold {{ $coupon->expires_at && $coupon->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : __('coupons.card.not_set') }}
                    </span>
                </div>
            </div>

            @if($coupon->expires_at && $coupon->expires_at->isPast())
            <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4">
                <p class="text-sm text-red-700">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    {{ __('coupons.card.expired') }}
                </p>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-2">
                <button 
                    type="button"
                    data-coupon-id="{{ $coupon->id }}"
                    data-action="edit"
                    class="coupon-action-btn flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-2 rounded-lg transition text-sm font-medium"
                    aria-label="{{ __('coupons.edit') }} {{ $coupon->code }}">
                    <i class="fas fa-edit ml-1"></i>
                    {{ __('coupons.edit') }}
                </button>
                @if($coupon->is_active)
                <button 
                    type="button"
                    data-coupon-id="{{ $coupon->id }}"
                    data-action="toggle"
                    class="coupon-action-btn flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg transition text-sm font-medium"
                    aria-label="{{ __('coupons.deactivate') }} {{ $coupon->code }}">
                    <i class="fas fa-times-circle ml-1"></i>
                    {{ __('coupons.deactivate') }}
                </button>
                @else
                <button 
                    type="button"
                    data-coupon-id="{{ $coupon->id }}"
                    data-action="toggle"
                    class="coupon-action-btn flex-1 bg-green-50 hover:bg-green-100 text-green-600 py-2 rounded-lg transition text-sm font-medium"
                    aria-label="{{ __('coupons.activate') }} {{ $coupon->code }}">
                    <i class="fas fa-check-circle ml-1"></i>
                    {{ __('coupons.activate') }}
                </button>
                @endif
                <button 
                    type="button"
                    data-coupon-id="{{ $coupon->id }}"
                    data-action="delete"
                    class="coupon-action-btn bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg transition"
                    aria-label="{{ __('coupons.delete') }} {{ $coupon->code }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('coupons.empty.title') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('coupons.empty.description') }}</p>
            <a href="{{ route('admin.coupons.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>{{ __('coupons.add_new') }}</span>
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($coupons->hasPages())
<div class="mt-6 bg-white rounded-lg shadow-sm p-4">
    <div class="flex items-center justify-center">
        {{ $coupons->links() }}
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    /* Pagination Links Styling */
    .pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .pagination a {
        background-color: white;
        color: #374151;
        border: 1px solid #e5e7eb;
        text-decoration: none;
    }
    
    .pagination a:hover {
        background-color: #f3f4f6;
        color: #6366f1;
        border-color: #6366f1;
    }
    
    .pagination span {
        background-color: #6366f1;
        color: white;
        border: 1px solid #6366f1;
        font-weight: 600;
    }
    
    .pagination .disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';
    
    // الحصول على CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    // إضافة معالج الأحداث لجميع أزرار الإجراءات
    document.addEventListener('DOMContentLoaded', function() {
        const actionButtons = document.querySelectorAll('.coupon-action-btn');
        
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const couponId = this.getAttribute('data-coupon-id');
                const action = this.getAttribute('data-action');
                
                if (!couponId || !action) {
                    console.error('Missing coupon ID or action');
                    return;
                }
                
                // التحقق من صحة ID (يجب أن يكون رقم)
                if (!/^\d+$/.test(couponId)) {
                    console.error('Invalid coupon ID');
                    return;
                }
                
                switch(action) {
                    case 'edit':
                        editCoupon(couponId);
                        break;
                    case 'toggle':
                        toggleCoupon(couponId, this);
                        break;
                    case 'delete':
                        deleteCoupon(couponId);
                        break;
                }
            });
        });
    });

    function editCoupon(couponId) {
        const url = `{{ url('/admin/coupons') }}/${couponId}/edit`;
        window.location.href = url;
    }

    function toggleCoupon(couponId, buttonElement) {
        if (!confirm('{{ __('coupons.messages.toggle_confirm') }}')) {
            return;
        }

        // تعطيل الزر أثناء المعالجة
        const originalContent = buttonElement.innerHTML;
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> {{ __('coupons.messages.processing') }}';

        fetch(`{{ url('/admin/coupons') }}/${couponId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || '{{ __('coupons.messages.error') }}');
                buttonElement.disabled = false;
                buttonElement.innerHTML = originalContent;
            }
        })
        .catch(error => {
            alert('{{ __('coupons.messages.toggle_error') }}');
            console.error('Error:', error);
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalContent;
        });
    }

    function deleteCoupon(couponId) {
        if (!confirm('{{ __('coupons.messages.delete_confirm') }}')) {
            return;
        }

        // العثور على الزر وإظهار حالة التحميل
        const button = document.querySelector(`[data-coupon-id="${couponId}"][data-action="delete"]`);
        if (button) {
            const originalContent = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`{{ url('/admin/coupons') }}/${couponId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || '{{ __('coupons.messages.delete_error') }}');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            }
        })
        .catch(error => {
            alert('{{ __('coupons.messages.delete_error') }}');
            console.error('Error:', error);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-trash"></i>';
            }
        });
    }
})();
</script>
@endpush

