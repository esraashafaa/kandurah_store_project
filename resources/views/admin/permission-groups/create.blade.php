@extends('layouts.admin')

@section('title', __('admin.permission_groups.create_title'))

@section('content')

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">
                <i class="fas fa-home ml-2"></i>
                {{ __('sidebar.home') }}
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <a href="{{ route('admin.permission-groups.index') }}" class="text-gray-600 hover:text-indigo-600">{{ __('admin.permission_groups.title') }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ __('admin.permission_groups.create_title') }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.permission_groups.create_title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('admin.permission_groups.create_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.permission-groups.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>{{ __('admin.permission_groups.back') }}</span>
    </a>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('admin.permission-groups.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <!-- Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.permission_groups.name_ar') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name_ar" 
                           name="name_ar" 
                           value="{{ old('name_ar') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name_ar') border-red-500 @enderror"
                           placeholder="مثال: إدارة الطلبات الكاملة"
                           required>
                    @error('name_ar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name_en" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.permission_groups.name_en') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name_en" 
                           name="name_en" 
                           value="{{ old('name_en') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name_en') border-red-500 @enderror"
                           placeholder="Example: Full Orders Management"
                           required>
                    @error('name_en')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.permission_groups.description_ar') }}
                    </label>
                    <textarea id="description_ar" 
                              name="description_ar" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description_ar') border-red-500 @enderror"
                              placeholder="وصف المجموعة بالعربية">{{ old('description_ar') }}</textarea>
                    @error('description_ar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description_en" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.permission_groups.description_en') }}
                    </label>
                    <textarea id="description_en" 
                              name="description_en" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description_en') border-red-500 @enderror"
                              placeholder="Group description in English">{{ old('description_en') }}</textarea>
                    @error('description_en')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Active Status -->
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    <span class="mr-3 text-sm font-medium text-gray-700">{{ __('admin.permission_groups.is_active') }}</span>
                </label>
            </div>

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    {{ __('admin.permission_groups.permissions') }} <span class="text-red-500">*</span>
                </label>
                
                <div class="space-y-4">
                    @foreach($permissions as $category => $categoryPermissions)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-semibold text-gray-800 capitalize">{{ $category }}</h4>
                            <button type="button" 
                                    onclick="toggleCategory('{{ $category }}')" 
                                    class="text-sm text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-check-double ml-1"></i>
                                {{ __('admin.permission_groups.select_all') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3" id="category-{{ $category }}">
                            @foreach($categoryPermissions as $permission)
                            <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}"
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 permission-checkbox"
                                       data-category="{{ $category }}">
                                <span class="mr-2 text-sm text-gray-700">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @error('permissions')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.permission-groups.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-times ml-2"></i>
                    {{ __('admin.permission_groups.cancel') }}
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-save ml-2"></i>
                    {{ __('admin.permission_groups.save_group') }}
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function toggleCategory(category) {
    const checkboxes = document.querySelectorAll(`#category-${category} .permission-checkbox`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}
</script>
@endpush
