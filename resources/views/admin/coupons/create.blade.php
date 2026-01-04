@extends('layouts.admin')

@section('title', 'إنشاء كوبون جديد')

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إنشاء كوبون جديد</h1>
        <p class="text-gray-600 mt-1">أضف كوبون خصم جديد للنظام</p>
    </div>
    <a href="{{ route('admin.coupons.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للقائمة</span>
    </a>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    كود الكوبون <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="code" 
                       name="code" 
                       value="{{ old('code') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror"
                       placeholder="مثال: SUMMER2024"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">يجب أن يكون الكود فريداً</p>
            </div>

            <!-- Discount Type -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">
                    نوع الخصم <span class="text-red-500">*</span>
                </label>
                <select id="discount_type" 
                        name="discount_type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount_type') border-red-500 @enderror"
                        required>
                    <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت (ريال)</option>
                </select>
                @error('discount_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">اختر نوع الخصم: نسبة مئوية أو مبلغ ثابت</p>
            </div>

            <!-- Discount -->
            <div>
                <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                    <span id="discount_label">نسبة الخصم (%)</span> <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="discount" 
                       name="discount" 
                       value="{{ old('discount') }}"
                       min="0" 
                       max="100" 
                       step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount') border-red-500 @enderror"
                       placeholder="10"
                       required>
                @error('discount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500" id="discount_hint">من 0 إلى 100</p>
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                    تاريخ الانتهاء
                </label>
                <input type="date" 
                       id="expires_at" 
                       name="expires_at" 
                       value="{{ old('expires_at') }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('expires_at') border-red-500 @enderror">
                @error('expires_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً إذا كان الكوبون غير محدود</p>
            </div>

            <!-- Max Usage -->
            <div>
                <label for="max_usage" class="block text-sm font-medium text-gray-700 mb-2">
                    الحد الأقصى للاستخدام
                </label>
                <input type="number" 
                       id="max_usage" 
                       name="max_usage" 
                       value="{{ old('max_usage') }}"
                       min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_usage') border-red-500 @enderror"
                       placeholder="100">
                @error('max_usage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً للاستخدام غير المحدود</p>
            </div>

            <!-- Min Purchase -->
            <div>
                <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-2">
                    الحد الأدنى للشراء (ريال)
                </label>
                <input type="number" 
                       id="min_purchase" 
                       name="min_purchase" 
                       value="{{ old('min_purchase') }}"
                       min="0" 
                       step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('min_purchase') border-red-500 @enderror"
                       placeholder="100.00">
                @error('min_purchase')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">الحد الأدنى لمبلغ الشراء لاستخدام الكوبون</p>
            </div>

            <!-- Is Active -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    حالة التفعيل
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">نشط</span>
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">الكوبونات غير النشطة لا يمكن استخدامها</p>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                الوصف
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                      placeholder="وصف الكوبون (اختياري)">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>حفظ الكوبون</span>
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <span>إلغاء</span>
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discount_type');
    const discountInput = document.getElementById('discount');
    const discountLabel = document.getElementById('discount_label');
    const discountHint = document.getElementById('discount_hint');

    function updateDiscountField() {
        const type = discountType.value;
        
        if (type === 'percentage') {
            discountLabel.textContent = 'نسبة الخصم (%)';
            discountInput.placeholder = '10';
            discountInput.max = '100';
            discountInput.min = '0';
            discountInput.step = '0.01';
            discountHint.textContent = 'من 0 إلى 100';
        } else {
            discountLabel.textContent = 'مبلغ الخصم (ريال)';
            discountInput.placeholder = '50';
            discountInput.removeAttribute('max');
            discountInput.min = '0';
            discountInput.step = '0.01';
            discountHint.textContent = 'أدخل المبلغ بالريال (مثل: 50 لخمسين ريال)';
        }
    }

    discountType.addEventListener('change', updateDiscountField);
    
    // تحديث الحقل عند تحميل الصفحة
    updateDiscountField();
});
</script>
@endpush

@endsection

