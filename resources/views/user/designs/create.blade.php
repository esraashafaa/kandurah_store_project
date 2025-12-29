@extends('layouts.user')

@section('title', 'إنشاء تصميم جديد')

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">إنشاء تصميم جديد</h1>
    <p class="text-gray-600 mt-1">قم بملء النموذج أدناه لإنشاء تصميم جديد</p>
</div>

<!-- Form -->
<form action="{{ route('my-designs.store') }}" method="POST" enctype="multipart/form-data">
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

                <!-- Description (EN/AR) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="description_en" class="block text-sm font-medium text-gray-700 mb-2">
                            الوصف (الإنجليزية) <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="description[en]" 
                            id="description_en" 
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description.en') border-red-500 @enderror"
                            required
                        >{{ old('description.en') }}</textarea>
                        @error('description.en')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">
                            الوصف (العربية) <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="description[ar]" 
                            id="description_ar" 
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description.ar') border-red-500 @enderror"
                            required
                        >{{ old('description.ar') }}</textarea>
                        @error('description.ar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        السعر (ر.س) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price" 
                        value="{{ old('price') }}"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('price') border-red-500 @enderror"
                        required
                    >
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Images -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        الصور <span class="text-red-500">*</span> <span class="text-gray-500 text-xs">(صورة واحدة على الأقل)</span>
                    </label>
                    <input 
                        type="file" 
                        name="images[]" 
                        id="images" 
                        multiple
                        accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('images') border-red-500 @enderror"
                        required
                        onchange="previewImages(this)"
                    >
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                <!-- Sizes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المقاسات المتاحة <span class="text-red-500">*</span> <span class="text-gray-500 text-xs">(مقاس واحد على الأقل)</span>
                    </label>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                        @foreach($sizes as $size)
                            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition @error('size_ids') border-red-500 @enderror">
                                <input 
                                    type="checkbox" 
                                    name="size_ids[]" 
                                    value="{{ $size->id }}"
                                    {{ in_array($size->id, old('size_ids', [])) ? 'checked' : '' }}
                                    class="ml-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <span class="text-sm font-medium">{{ $size->code }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('size_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Design Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        خيارات التصميم <span class="text-gray-500 text-xs">(اختياري)</span>
                    </label>
                    @foreach($designOptions as $type => $group)
                        @if($group['options']->count() > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $group['label_ar'] }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($group['options'] as $option)
                                        <label class="flex items-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                                            <input 
                                                type="checkbox" 
                                                name="design_option_ids[]" 
                                                value="{{ $option->id }}"
                                                {{ in_array($option->id, old('design_option_ids', [])) ? 'checked' : '' }}
                                                class="ml-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                            >
                                            <span class="text-sm">{{ $option->getTranslation('name', 'ar') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
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
                        تفعيل التصميم (سيظهر للآخرين)
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
                        <span>يجب إدخال الاسم والوصف باللغتين العربية والإنجليزية</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>يجب رفع صورة واحدة على الأقل</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>يجب اختيار مقاس واحد على الأقل</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <span>خيارات التصميم اختيارية ويمكن اختيار عدة خيارات</span>
                    </li>
                </ul>

                <div class="pt-4 border-t">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التصميم
                    </button>
                    <a href="{{ route('my-designs.index') }}" class="block mt-2 text-center text-gray-600 hover:text-gray-800 text-sm">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover rounded-lg">
                    <span class="absolute top-1 right-1 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">${index + 1}</span>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush

