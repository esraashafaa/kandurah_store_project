@extends('layouts.admin')

@section('title', __('coupons.create_title'))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('coupons.create_title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('coupons.create_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.coupons.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>{{ __('coupons.back_to_list') }}</span>
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
                    {{ __('coupons.fields.code') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="code" 
                       name="code" 
                       value="{{ old('code') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror"
                       placeholder="{{ __('coupons.fields.code_placeholder') }}"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.code_hint') }}</p>
            </div>

            <!-- Discount Type -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('coupons.fields.discount_type') }} <span class="text-red-500">*</span>
                </label>
                <select id="discount_type" 
                        name="discount_type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount_type') border-red-500 @enderror"
                        required>
                    <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>{{ __('coupons.fields.discount_type_percentage') }}</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('coupons.fields.discount_type_fixed') }}</option>
                </select>
                @error('discount_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.discount_type_hint') }}</p>
            </div>

            <!-- Discount -->
            <div>
                <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                    <span id="discount_label">{{ __('coupons.fields.discount') }}</span> <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="discount" 
                       name="discount" 
                       value="{{ old('discount') }}"
                       min="0" 
                       max="100" 
                       step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount') border-red-500 @enderror"
                       placeholder="{{ __('coupons.fields.discount_placeholder') }}"
                       required>
                @error('discount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500" id="discount_hint">{{ __('coupons.fields.discount_hint_percentage') }}</p>
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('coupons.fields.expires_at') }}
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
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.expires_at_hint') }}</p>
            </div>

            <!-- Max Usage -->
            <div>
                <label for="max_usage" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('coupons.fields.max_usage') }}
                </label>
                <input type="number" 
                       id="max_usage" 
                       name="max_usage" 
                       value="{{ old('max_usage') }}"
                       min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_usage') border-red-500 @enderror"
                       placeholder="{{ __('coupons.fields.max_usage_placeholder') }}">
                @error('max_usage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.max_usage_hint') }}</p>
            </div>

            <!-- Min Purchase -->
            <div>
                <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('coupons.fields.min_purchase') }}
                </label>
                <input type="number" 
                       id="min_purchase" 
                       name="min_purchase" 
                       value="{{ old('min_purchase') }}"
                       min="0" 
                       step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('min_purchase') border-red-500 @enderror"
                       placeholder="{{ __('coupons.fields.min_purchase_placeholder') }}">
                @error('min_purchase')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.min_purchase_hint') }}</p>
            </div>

            <!-- Is Active -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('coupons.fields.is_active') }}
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">{{ __('coupons.fields.is_active_label') }}</span>
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">{{ __('coupons.fields.is_active_hint') }}</p>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('coupons.fields.description') }}
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                      placeholder="{{ __('coupons.fields.description_placeholder') }}">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>{{ __('coupons.save') }}</span>
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <span>{{ __('common.cancel') }}</span>
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
            discountLabel.textContent = '{{ __('coupons.fields.discount') }}';
            discountInput.placeholder = '{{ __('coupons.fields.discount_placeholder') }}';
            discountInput.max = '100';
            discountInput.min = '0';
            discountInput.step = '0.01';
            discountHint.textContent = '{{ __('coupons.fields.discount_hint_percentage') }}';
        } else {
            discountLabel.textContent = '{{ __('coupons.fields.discount_fixed') }}';
            discountInput.placeholder = '50';
            discountInput.removeAttribute('max');
            discountInput.min = '0';
            discountInput.step = '0.01';
            discountHint.textContent = '{{ __('coupons.fields.discount_hint_fixed') }}';
        }
    }

    discountType.addEventListener('change', updateDiscountField);
    
    // Update field on page load
    updateDiscountField();
});
</script>
@endpush

@endsection

