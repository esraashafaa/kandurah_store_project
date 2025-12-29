@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@push('styles')
<style>
    .search-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .search-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إدارة المستخدمين</h1>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع مستخدمي المتجر</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
            <i class="fas fa-plus"></i>
            <span>إضافة مستخدم</span>
        </a>
    </div>
</div>


<!-- Statistics Cards -->
<div class="w-full flex gap-4 mb-8 flex-nowrap items-stretch">
    
    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-blue-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-blue-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">إجمالي المستخدمين</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-green-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-check text-green-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">المستخدمين النشطين</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-purple-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-shield text-purple-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">المشرفين</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['admins'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-orange-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-plus text-orange-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">مستخدمين جدد اليوم</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['new_today'] ?? 0 }}</p>
        </div>
    </div>

</div>


<!-- Filters & Search - Single Line -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col lg:flex-row gap-4">
        
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ابحث بالاسم أو البريد الإلكتروني أو الهاتف..."
                    class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >
                <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
            </div>
        </div>

        <!-- Role Filter -->
        <div class="w-full lg:w-48">
            <select name="role" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع الأدوار</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>مستخدم</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>مشرف</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>مشرف عام</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full lg:w-48">
            <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="search-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                <i class="fas fa-filter"></i>
                <span class="hidden sm:inline">بحث</span>
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
                <i class="fas fa-redo"></i>
                <span class="hidden sm:inline">إعادة تعيين</span>
            </a>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">البريد الإلكتروني</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الهاتف</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المحفظة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الدور</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ التسجيل</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-900">{{ $user->email }}</span>
                            @if($user->email_verified_at)
                                <i class="fas fa-check-circle text-green-500 text-xs" title="تم التحقق"></i>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-900">{{ $user->phone ?? 'غير محدد' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wallet text-indigo-500 text-sm"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($user->wallet_balance ?? 0, 2) }} ريال</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                            @if($user->role->value === 'super_admin') bg-red-100 text-red-900
                            @elseif($user->role->value === 'admin') bg-purple-100 text-purple-900
                            @else bg-blue-100 text-blue-900
                            @endif
                        ">
                            <i class="fas 
                                @if($user->role->value === 'super_admin') fa-crown
                                @elseif($user->role->value === 'admin') fa-user-shield
                                @else fa-user
                                @endif
                            "></i>
                            {{ __('users.roles.' . $user->role->value) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                {{ $user->is_active ? 'checked' : '' }}
                                class="sr-only peer"
                                onchange="toggleUserStatus({{ $user->id }}, this.checked)"
                            >
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-green-600"></div>
                        </label>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $user->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 hover:bg-blue-50 rounded-lg" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200 p-2 hover:bg-indigo-50 rounded-lg" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(Auth::id() !== $user->id)
                            <button 
                                onclick="deleteUser({{ $user->id }})" 
                                class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 hover:bg-red-50 rounded-lg"
                                title="حذف"
                            >
                                <i class="fas fa-trash" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-users text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-semibold mb-2 text-gray-700">لا توجد مستخدمين</p>
                            <p class="text-sm text-gray-500">قم بإضافة مستخدم جديد للبدء</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                عرض 
                <span class="font-semibold">{{ $users->firstItem() }}</span>
                إلى 
                <span class="font-semibold">{{ $users->lastItem() }}</span>
                من 
                <span class="font-semibold">{{ $users->total() }}</span>
                مستخدم
            </div>
            <div class="flex items-center gap-2">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Font Awesome icons are now handled globally in admin.blade.php
// No need for duplicate code here

function toggleUserStatus(userId, isActive) {
    if (!confirm('هل أنت متأكد من تغيير حالة المستخدم؟')) {
        event.target.checked = !isActive;
        return;
    }

    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ is_active: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'تم تحديث حالة المستخدم بنجاح');
        } else {
            event.target.checked = !isActive;
            showNotification('error', data.message || 'حدث خطأ أثناء تحديث حالة المستخدم');
        }
    })
    .catch(error => {
        event.target.checked = !isActive;
        showNotification('error', 'حدث خطأ أثناء تحديث حالة المستخدم');
        console.error('Error:', error);
    });
}

function deleteUser(userId) {
    if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.')) {
        return;
    }

    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'تم حذف المستخدم بنجاح');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'حدث خطأ أثناء حذف المستخدم');
        }
    })
    .catch(error => {
        showNotification('error', 'حدث خطأ أثناء حذف المستخدم');
        console.error('Error:', error);
    });
}

function showNotification(type, message) {
    // يمكنك استبدال هذا بنظام إشعارات أفضل
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 transition-all duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush