@extends('layouts.admin')

@section('title', __('admin.admins.edit_title'))

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

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">
                <i class="fas fa-home ml-2"></i>
                {{ __('admin.admins.home') }}
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <a href="{{ route('admin.admins.index') }}" class="text-gray-600 hover:text-indigo-600">{{ __('sidebar.admins') }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ __('admin.admins.edit_title') }}: {{ $admin->name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.admins.edit_title') }}</h1>
    <p class="text-gray-600 mt-1">{{ __('admin.admins.edit_subtitle') }}</p>
</div>

<!-- Form -->
<form action="{{ route('admin.admins.update', $admin) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3">{{ __('admin.admins.basic_info') }}</h2>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.admins.full_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $admin->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.admins.email_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $admin->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.admins.password_label') }} ({{ __('admin.admins.password_placeholder') }})
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                        >
                        <button type="button" onclick="togglePassword('password')" class="absolute left-3 top-2.5 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.admins.password_confirm') }}
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute left-3 top-2.5 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Role & Status -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('admin.admins.role_and_status') }}</h2>
                
                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.admins.role_label') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="role" 
                        id="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role') border-red-500 @enderror"
                        required
                    >
                        <option value="admin" {{ old('role', $admin->role->value) === 'admin' ? 'selected' : '' }}>{{ __('admin.admins.role_admin') }}</option>
                        <option value="super_admin" {{ old('role', $admin->role->value) === 'super_admin' ? 'selected' : '' }}>{{ __('admin.admins.role_super_admin') }}</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', $admin->is_active) ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="mr-3 text-sm font-medium text-gray-700">{{ __('admin.admins.active_account') }}</span>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <!-- Permissions Management Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6" id="permissions">
        <h2 class="text-xl font-bold text-gray-800 mb-6">{{ __('admin.admins.permissions_management') }}</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Permission Groups -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    {{ __('admin.admins.permission_groups_label') }}
                </label>
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @forelse($permissionGroups ?? [] as $group)
                    <label class="flex items-start p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200 group-checkbox" data-group-id="{{ $group->id }}">
                        <input type="checkbox" 
                               name="permission_groups[]" 
                               value="{{ $group->id }}"
                               class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 group-checkbox-input"
                               data-permissions="{{ $group->permissions->pluck('id')->toJson() }}"
                               {{ in_array($group->id, old('permission_groups', $userGroups ?? [])) ? 'checked' : '' }}>
                        <div class="mr-3 flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $group->getName() }}</p>
                            <p class="text-xs text-gray-500">{{ $group->permissions->count() }} {{ __('admin.admins.permissions_count') }}</p>
                        </div>
                    </label>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('admin.admins.no_permission_groups_available') }}</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.permission-groups.index') }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                    <i class="fas fa-external-link-alt ml-1"></i>
                    {{ __('admin.admins.manage_permission_groups') }}
                </a>
            </div>

            <!-- Individual Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    {{ __('admin.admins.individual_permissions_label') }}
                </label>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($permissions ?? [] as $category => $categoryPermissions)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-800 capitalize">{{ $category }}</h4>
                            <button type="button" 
                                    onclick="toggleCategoryPermissions('{{ $category }}')" 
                                    class="text-xs text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-check-double ml-1"></i>
                                {{ __('admin.admins.select_all') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="permissions-category-{{ $category }}">
                            @foreach($categoryPermissions as $permission)
                            <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}"
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 permission-checkbox"
                                       data-category="{{ $category }}"
                                       {{ in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }}>
                                <span class="mr-2 text-xs text-gray-700">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end gap-4 mt-6">
        <a href="{{ route('admin.admins.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition">
            <i class="fas fa-times ml-2"></i>
            {{ __('admin.admins.back_to_list') }}
        </a>
        <button type="submit" class="save-btn text-white px-6 py-3 rounded-lg font-medium transition">
            <i class="fas fa-save ml-2"></i>
            {{ __('admin.admins.save_changes') }}
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// إدارة مجموعات الصلاحيات
document.addEventListener('DOMContentLoaded', function() {
    const groupCheckboxes = document.querySelectorAll('.group-checkbox-input');
    groupCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permissions = JSON.parse(this.dataset.permissions);
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            
            if (this.checked) {
                permissions.forEach(permId => {
                    const permCheckbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
                    if (permCheckbox) {
                        permCheckbox.checked = true;
                    }
                });
            } else {
                permissions.forEach(permId => {
                    const permCheckbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
                    if (permCheckbox) {
                        let isInOtherGroup = false;
                        groupCheckboxes.forEach(otherGroup => {
                            if (otherGroup !== this && otherGroup.checked) {
                                const otherPermissions = JSON.parse(otherGroup.dataset.permissions);
                                if (otherPermissions.includes(permId)) {
                                    isInOtherGroup = true;
                                }
                            }
                        });
                        
                        if (!isInOtherGroup) {
                            permCheckbox.checked = false;
                        }
                    }
                });
            }
        });
    });
});

function toggleCategoryPermissions(category) {
    const checkboxes = document.querySelectorAll(`#permissions-category-${category} .permission-checkbox`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

// الانتقال إلى قسم الصلاحيات إذا كان هناك hash في URL
if (window.location.hash === '#permissions') {
    document.getElementById('permissions')?.scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush
