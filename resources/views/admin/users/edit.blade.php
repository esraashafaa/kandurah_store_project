@extends('layouts.admin')

@section('title', 'تعديل المستخدم')

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
                الرئيسية
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-indigo-600">المستخدمين</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-indigo-600">{{ $user->name }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">تعديل</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">تعديل المستخدم</h1>
        <p class="text-gray-600 mt-1">قم بتعديل معلومات المستخدم أدناه</p>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
        <i class="fas fa-eye"></i>
        <span>عرض التفاصيل</span>
    </a>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-circle"></i>
            <span class="font-bold">حدثت الأخطاء التالية:</span>
        </div>
        <ul class="list-disc list-inside mr-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form -->
<form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3">المعلومات الأساسية</h2>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        الاسم الكامل <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $user->name) }}"
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
                        البريد الإلكتروني <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        رقم الهاتف
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                        placeholder="+966XXXXXXXXX"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور <span class="text-gray-500 text-xs">(اتركه فارغاً إذا لم ترد تغييره)</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
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
                        تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="••••••••"
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
            
            <!-- Profile Image -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">صورة الملف الشخصي</h2>
                
                <div class="flex flex-col items-center">
                    <div id="image-preview" class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        @endif
                    </div>
                    
                    <label for="profile_image" class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-upload ml-2"></i>
                        تغيير الصورة
                    </label>
                    <input 
                        type="file" 
                        name="profile_image" 
                        id="profile_image" 
                        class="hidden"
                        accept="image/*"
                        onchange="previewImage(this)"
                    >
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG أو GIF (حد أقصى 2MB)</p>
                    
                    @error('profile_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role & Status -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">الدور والحالة</h2>
                
                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        الدور <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="role" 
                        id="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role') border-red-500 @enderror"
                        required
                    >
                        @php
                            $currentRole = old('role', $user->role->value);
                        @endphp
                        <option value="user" {{ $currentRole === 'user' ? 'selected' : '' }}>مستخدم عادي</option>
                        <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>مشرف</option>
                        @can('manage-admins')
                        <option value="super_admin" {{ $currentRole === 'super_admin' ? 'selected' : '' }}>مشرف عام</option>
                        @endcan
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="mr-3 text-sm font-medium text-gray-700">حساب نشط</span>
                    </label>
                </div>

                <!-- Email Verified -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="email_verified" 
                            value="1"
                            {{ old('email_verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="mr-3 text-sm font-medium text-gray-700">بريد مؤكد</span>
                    </label>
                </div>
            </div>

            <!-- Permissions Management (Only for Super Admin and when role is admin/super_admin) -->
            @if(auth()->user()->role === \App\Enums\RoleEnum::SUPER_ADMIN)
            <div class="bg-white rounded-xl shadow-sm p-6" id="permissions-section" style="display: {{ in_array($user->role->value, ['admin', 'super_admin']) ? 'block' : 'none' }};">
                <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة الصلاحيات</h2>
                
                <!-- Permission Groups -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        مجموعات الصلاحيات
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @forelse($permissionGroups ?? [] as $group)
                        <label class="flex items-start p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200 group-checkbox" data-group-id="{{ $group->id }}">
                            <input type="checkbox" 
                                   name="permission_groups[]" 
                                   value="{{ $group->id }}"
                                   {{ in_array($group->id, $userGroups ?? []) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 group-checkbox-input"
                                   data-permissions="{{ $group->permissions->pluck('id')->toJson() }}">
                            <div class="mr-3 flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $group->getName('ar') }}</p>
                                <p class="text-xs text-gray-500">{{ $group->permissions->count() }} صلاحية</p>
                            </div>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد مجموعات صلاحيات متاحة</p>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.permission-groups.index') }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                        <i class="fas fa-external-link-alt ml-1"></i>
                        إدارة مجموعات الصلاحيات
                    </a>
                </div>

                <!-- Individual Permissions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        الصلاحيات الفردية
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
                                    تحديد الكل
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="permissions-category-{{ $category }}">
                                @foreach($categoryPermissions as $permission)
                                <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, $userPermissions ?? []) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 permission-checkbox"
                                           data-category="{{ $category }}">
                                    <span class="mr-2 text-xs text-gray-700">{{ $permission->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Wallet Balance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">رصيد المحفظة</h2>
                
                <div>
                    <label for="wallet_balance" class="block text-sm font-medium text-gray-700 mb-2">
                        الرصيد (ريال)
                    </label>
                    <input 
                        type="number" 
                        name="wallet_balance" 
                        id="wallet_balance" 
                        value="{{ old('wallet_balance', $user->wallet_balance ?? 0) }}"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('wallet_balance') border-red-500 @enderror"
                    >
                    @error('wallet_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end gap-4 mt-6">
        <a href="{{ route('admin.users.show', $user) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition">
            <i class="fas fa-times ml-2"></i>
            إلغاء
        </a>
        <button type="submit" class="save-btn text-white px-6 py-3 rounded-lg font-medium transition">
            <i class="fas fa-save ml-2"></i>
            حفظ التعديلات
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        // إعادة عرض الصورة الحالية إذا لم يتم اختيار صورة جديدة
        @if($user->avatar)
            preview.innerHTML = `<img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">`;
        @else
            preview.innerHTML = `<i class="fas fa-user text-4xl text-gray-400"></i>`;
        @endif
    }
}

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

// إدارة قسم الصلاحيات
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const permissionsSection = document.getElementById('permissions-section');
    
    if (roleSelect && permissionsSection) {
        // عرض/إخفاء قسم الصلاحيات حسب الدور
        function togglePermissionsSection() {
            const selectedRole = roleSelect.value;
            if (selectedRole === 'admin' || selectedRole === 'super_admin') {
                permissionsSection.style.display = 'block';
            } else {
                permissionsSection.style.display = 'none';
            }
        }
        
        roleSelect.addEventListener('change', togglePermissionsSection);
        
        // إدارة مجموعات الصلاحيات
        const groupCheckboxes = document.querySelectorAll('.group-checkbox-input');
        groupCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const permissions = JSON.parse(this.dataset.permissions);
                const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
                
                if (this.checked) {
                    // تحديد جميع صلاحيات المجموعة
                    permissions.forEach(permId => {
                        const permCheckbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
                        if (permCheckbox) {
                            permCheckbox.checked = true;
                        }
                    });
                } else {
                    // إلغاء تحديد صلاحيات المجموعة (فقط إذا لم تكن مختارة من مجموعة أخرى)
                    permissions.forEach(permId => {
                        const permCheckbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
                        if (permCheckbox) {
                            // التحقق من أن الصلاحية ليست في مجموعة أخرى مختارة
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
    }
});

function toggleCategoryPermissions(category) {
    const checkboxes = document.querySelectorAll(`#permissions-category-${category} .permission-checkbox`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}
</script>
@endpush

