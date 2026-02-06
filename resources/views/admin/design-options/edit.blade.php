@extends('layouts.admin')

@section('title', __('admin.design_options.edit_title'))

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.design_options.edit_title') }}</h1>
    <p class="text-gray-600 mt-1">{{ __('admin.design_options.edit_subtitle') }}</p>
</div>

<!-- Form -->
<form action="{{ route('dashboard.design-options.update', $option) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3">{{ __('admin.design_options.basic_info') }}</h2>

                <!-- Name (EN/AR) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name_en" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('common.name_english') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name[en]" 
                            id="name_en" 
                            value="{{ old('name.en', $option->getTranslation('name', 'en')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name.en') border-red-500 @enderror"
                            required
                        >
                        @error('name.en')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('common.name_arabic') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name[ar]" 
                            id="name_ar" 
                            value="{{ old('name.ar', $option->getTranslation('name', 'ar')) }}"
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
                        {{ __('admin.design_options.type_label') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="type" 
                        id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('type') border-red-500 @enderror"
                        required
                    >
                        <option value="">{{ __('admin.design_options.select_type') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type['value'] }}" {{ old('type', $option->type->value) == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label_ar'] }} ({{ $type['label'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">{{ __('admin.design_options.current_type') }}: <span class="font-semibold">{{ $option->type->labelAr() }}</span></p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active" 
                        value="1"
                        {{ old('is_active', $option->is_active) ? 'checked' : '' }}
                        class="ml-2 w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        {{ __('admin.design_options.activate_option') }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4 sticky top-4">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3">{{ __('admin.design_options.option_info') }}</h3>
                
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-600">{{ __('admin.design_options.type_label') }}:</span>
                        <span class="font-semibold text-gray-900">{{ $option->type->labelAr() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ __('admin.design_options.status') }}:</span>
                        @if($option->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">{{ __('common.active') }}</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">{{ __('common.inactive') }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-600">{{ __('admin.design_options.designs_count_label') }}:</span>
                        <span class="font-semibold text-gray-900">{{ $option->designs()->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ __('admin.design_options.created_at') }}:</span>
                        <span class="font-semibold text-gray-900">{{ $option->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        <i class="fas fa-save ml-2"></i>
                        {{ __('admin.design_options.save_changes') }}
                    </button>
                    <a href="{{ route('dashboard.design-options.show', $option) }}" class="block mt-2 text-center text-gray-600 hover:text-gray-800 text-sm">
                        {{ __('common.cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

