@extends('layouts.admin')

@section('title', 'إضافة خيار تصميم جديد')

@push('styles')
<style>
    .save-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .save-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إضافة خيار تصميم جديد</h1>
        <p class="text-gray-600 mt-1">قم بملء النموذج أدناه لإضافة خيار تصميم جديد</p>
    </div>
    <a href="{{ route('dashboard.design-options.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للقائمة</span>
    </a>
</div>

<!-- Form -->
<form action="{{ route('dashboard.design-options.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3">المعلومات الأساسية</h2>

                <!-- Name (EN/AR) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name_en" class="block text-sm font-medium text-gray-700 mb-2">
                            الاسم (الإنجليزية) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name[en]" 
                            id="name_en" 
                            value="{{ old('name.en') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name.en') border-red-500 @enderror"
                            required
                        >
                        @error('name.en')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">
                            الاسم (العربية) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name[ar]" 
                            id="name_ar" 
                            value="{{ old('name.ar') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name.ar') border-red-500 @enderror"
                            required
                        >
                        @error('name.ar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        النوع <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="type" 
                        id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('type') border-red-500 @enderror"
                        required
                    >
                        <option value="">اختر النوع</option>
                        @foreach($types as $type)
                            <option value="{{ $type['value'] }}" {{ old('type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label_ar'] }} ({{ $type['label'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="ml-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        تفعيل الخيار (سيظهر للمستخدمين)
                    </label>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4 sticky top-4">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3">إرشادات</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>يجب إدخال الاسم باللغتين العربية والإنجليزية</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <span>اختر النوع المناسب (لون، نوع قماش، نوع قبة، نوع أكمام)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                        <span>يمكنك إضافة خيارات غير محدودة لكل نوع</span>
                    </li>
                </ul>

            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end gap-4 mt-6">
        <a href="{{ route('dashboard.design-options.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
            <i class="fas fa-times"></i>
            <span>إلغاء</span>
        </a>
        <button type="submit" class="save-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>إضافة الخيار</span>
        </button>
    </div>

</form>

@endsection

