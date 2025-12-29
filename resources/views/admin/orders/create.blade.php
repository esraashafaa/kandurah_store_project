@extends('layouts.admin')

@section('title', 'إنشاء طلب جديد')

@push('styles')
<style>
    .item-card {
        transition: all 0.3s ease;
    }
    .item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إنشاء طلب جديد</h1>
        <p class="text-gray-600 mt-1">إضافة طلب جديد للعميل</p>
    </div>
    <a href="{{ route('dashboard.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للقائمة</span>
    </a>
</div>

<form action="{{ route('dashboard.orders.store') }}" method="POST" id="orderForm">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Order Items -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Customer Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-indigo-600"></i>
                    اختيار العميل
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" id="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                        <option value="">اختر العميل</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Location Selection (will be loaded via AJAX) -->
                <div id="locationSection" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان التوصيل *</label>
                    <select name="location_id" id="location_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                        <option value="">اختر العنوان</option>
                    </select>
                    @error('location_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-indigo-600"></i>
                        عناصر الطلب
                    </h2>
                    <button type="button" onclick="addOrderItem()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>إضافة عنصر</span>
                    </button>
                </div>
                
                <div id="orderItemsContainer" class="space-y-4">
                    <!-- Items will be added here dynamically -->
                </div>
                
                @error('items')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order Notes -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-600"></i>
                    ملاحظات الطلب
                </h2>
                <textarea 
                    name="notes" 
                    rows="4" 
                    placeholder="أضف أي ملاحظات إضافية للطلب..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >{{ old('notes') }}</textarea>
            </div>

        </div>

        <!-- Right Column - Order Summary -->
        <div class="space-y-6">
            
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">ملخص الطلب</h2>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">عدد العناصر</span>
                        <span class="font-semibold text-gray-900" id="itemsCount">0</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">المجموع الفرعي</span>
                        <span class="font-semibold text-gray-900" id="subtotal">0.00 ر.س</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-lg font-bold text-gray-900">الإجمالي</span>
                        <span class="text-2xl font-bold text-indigo-600" id="total">0.00 ر.س</span>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                    <i class="fas fa-check ml-2"></i>
                    إنشاء الطلب
                </button>
            </div>

        </div>
    </div>
</form>

<!-- Template for Order Item -->
<template id="orderItemTemplate">
    <div class="item-card bg-gray-50 rounded-xl p-4 border border-gray-200" data-item-index="">
        <div class="flex justify-between items-start mb-4">
            <h3 class="font-semibold text-gray-900">عنصر #<span class="item-number">1</span></h3>
            <button type="button" onclick="removeOrderItem(this)" class="text-red-600 hover:text-red-900 transition-colors">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Design Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التصميم *</label>
                <select name="items[INDEX][design_id]" required class="item-design-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">اختر التصميم</option>
                    @foreach($designs as $design)
                        <option value="{{ $design->id }}" data-price="{{ $design->price }}">
                            {{ $design->getTranslation('name', 'ar') ?? 'تصميم' }} - {{ number_format($design->price, 2) }} ر.س
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Size Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المقاس *</label>
                <select name="items[INDEX][size_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">اختر المقاس</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}">
                            {{ $size->code }} - {{ $size->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكمية *</label>
                <input 
                    type="number" 
                    name="items[INDEX][quantity]" 
                    value="1" 
                    min="1" 
                    required
                    class="item-quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    onchange="calculateTotal()"
                >
            </div>
            
            <!-- Design Options -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">خيارات التصميم <span class="text-gray-500 text-xs">(اختياري)</span></label>
                @php
                    $designOptionsByType = $designOptions->groupBy('type');
                @endphp
                @foreach($designOptionsByType as $type => $options)
                    <div class="mb-3">
                        <h4 class="text-xs font-semibold text-gray-600 mb-2">
                            @if($type === 'color') اللون
                            @elseif($type === 'dome_type') نوع القبة
                            @elseif($type === 'fabric_type') نوع القماش
                            @elseif($type === 'sleeve_type') نوع الأكمام
                            @else {{ $type }}
                            @endif
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($options as $option)
                                <label class="flex items-center px-3 py-1.5 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition text-sm">
                                    <input 
                                        type="checkbox" 
                                        name="items[INDEX][design_option_ids][]" 
                                        value="{{ $option->id }}"
                                        class="ml-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    >
                                    <span>{{ $option->getTranslation('name', 'ar') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Item Subtotal -->
            <div class="pt-3 border-t border-gray-300">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">المجموع الفرعي</span>
                    <span class="font-semibold text-indigo-600 item-subtotal">0.00 ر.س</span>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let itemIndex = 0;

// Load locations when user is selected
document.getElementById('user_id').addEventListener('change', function() {
    const userId = this.value;
    const locationSection = document.getElementById('locationSection');
    const locationSelect = document.getElementById('location_id');
    
    if (userId) {
        fetch(`/dashboard/orders/user/${userId}/locations`)
            .then(response => response.json())
            .then(data => {
                locationSelect.innerHTML = '<option value="">اختر العنوان</option>';
                if (data.data && data.data.length > 0) {
                    data.data.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = `${location.city} - ${location.area} - ${location.street}`;
                        locationSelect.appendChild(option);
                    });
                    locationSection.classList.remove('hidden');
                } else {
                    locationSection.classList.add('hidden');
                    alert('لا توجد عناوين مسجلة لهذا المستخدم');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                locationSection.classList.add('hidden');
            });
    } else {
        locationSection.classList.add('hidden');
    }
});

// Add new order item
function addOrderItem() {
    const container = document.getElementById('orderItemsContainer');
    const template = document.getElementById('orderItemTemplate');
    const newItem = template.content.cloneNode(true);
    
    // Replace INDEX with actual index
    const itemHtml = newItem.querySelector('.item-card').outerHTML;
    const updatedHtml = itemHtml.replace(/INDEX/g, itemIndex);
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = updatedHtml;
    const newItemElement = tempDiv.firstElementChild;
    newItemElement.setAttribute('data-item-index', itemIndex);
    newItemElement.querySelector('.item-number').textContent = itemIndex + 1;
    
    container.appendChild(newItemElement);
    
    // Add event listeners
    const designSelect = newItemElement.querySelector('.item-design-select');
    const quantityInput = newItemElement.querySelector('.item-quantity');
    
    if (designSelect) {
        designSelect.addEventListener('change', calculateItemSubtotal);
    }
    if (quantityInput) {
        quantityInput.addEventListener('input', calculateItemSubtotal);
    }
    
    itemIndex++;
    calculateTotal();
}

// Remove order item
function removeOrderItem(button) {
    const itemCard = button.closest('.item-card');
    itemCard.remove();
    calculateTotal();
    updateItemNumbers();
}

// Update item numbers
function updateItemNumbers() {
    const items = document.querySelectorAll('.item-card');
    items.forEach((item, index) => {
        item.querySelector('.item-number').textContent = index + 1;
    });
}

// Calculate item subtotal
function calculateItemSubtotal(event) {
    const itemCard = event.target.closest('.item-card');
    const designSelect = itemCard.querySelector('.item-design-select');
    const quantityInput = itemCard.querySelector('.item-quantity');
    const subtotalSpan = itemCard.querySelector('.item-subtotal');
    
    const price = parseFloat(designSelect.selectedOptions[0]?.dataset.price || 0);
    const quantity = parseInt(quantityInput.value || 0);
    const subtotal = price * quantity;
    
    subtotalSpan.textContent = subtotal.toFixed(2) + ' ر.س';
    calculateTotal();
}

// Calculate total
function calculateTotal() {
    const items = document.querySelectorAll('.item-card');
    let total = 0;
    let itemsCount = 0;
    
    items.forEach(item => {
        const designSelect = item.querySelector('.item-design-select');
        const quantityInput = item.querySelector('.item-quantity');
        
        if (designSelect && quantityInput) {
            const price = parseFloat(designSelect.selectedOptions[0]?.dataset.price || 0);
            const quantity = parseInt(quantityInput.value || 0);
            total += price * quantity;
            if (price > 0 && quantity > 0) {
                itemsCount++;
            }
        }
    });
    
    document.getElementById('itemsCount').textContent = items.length;
    document.getElementById('subtotal').textContent = total.toFixed(2) + ' ر.س';
    document.getElementById('total').textContent = total.toFixed(2) + ' ر.س';
}

// Form validation
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-card');
    if (items.length === 0) {
        e.preventDefault();
        alert('يجب إضافة عنصر واحد على الأقل للطلب');
        return false;
    }
    
    let hasValidItem = false;
    items.forEach(item => {
        const designSelect = item.querySelector('.item-design-select');
        const sizeSelect = item.querySelector('select[name*="[size_id]"]');
        const quantityInput = item.querySelector('.item-quantity');
        
        if (designSelect.value && sizeSelect.value && quantityInput.value > 0) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        e.preventDefault();
        alert('يجب ملء جميع الحقول المطلوبة لكل عنصر');
        return false;
    }
});

// Add first item on page load
window.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});
</script>
@endpush

