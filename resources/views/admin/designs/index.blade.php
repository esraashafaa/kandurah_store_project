@extends('layouts.admin')

@section('title', 'إدارة التصاميم')

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
        <h1 class="text-3xl font-bold text-gray-900">إدارة التصاميم</h1>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع تصاميم الكندرة</p>
    </div>
    <button onclick="exportDesigns()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
        <i class="fas fa-file-excel"></i>
        <span>تصدير Excel</span>
    </button>
</div>

<!-- Statistics Cards -->
<div class="w-full flex gap-4 mb-8 flex-nowrap items-stretch">
    
    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-blue-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-palette text-blue-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">إجمالي التصاميم</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-purple-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-plus-circle text-purple-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">تصاميم اليوم</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['today'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-green-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">مع طلبات</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['with_orders'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-orange-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-orange-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">مستخدمين مميزين</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['unique_users'] ?? 0 }}</p>
        </div>
    </div>

</div>

<!-- Filters & Search - Single Line -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="GET" action="{{ route('dashboard.designs.index') }}" class="flex flex-col lg:flex-row gap-4">
        
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ابحث باسم التصميم أو اسم المستخدم..."
                    class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >
                <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
            </div>
        </div>

        <!-- Size Filter -->
        <div class="w-full lg:w-48">
            <select name="size_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع المقاسات</option>
                @foreach($sizes ?? [] as $size)
                    <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>
                        {{ $size->code }} - {{ $size->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- User Filter -->
        <div class="w-full lg:w-48">
            <select name="user_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع المستخدمين</option>
                @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full lg:w-48">
            <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">جميع الحالات</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="search-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                <i class="fas fa-filter"></i>
                <span class="hidden sm:inline">بحث</span>
            </button>
            <a href="{{ route('dashboard.designs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
                <i class="fas fa-redo"></i>
                <span class="hidden sm:inline">إعادة تعيين</span>
            </a>
        </div>
    </form>
</div>

<!-- Designs Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">التصميم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الصورة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">السعر</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المقاسات</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ الإنشاء</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($designs as $design)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $design->getTranslation('name', 'ar') ?? 'تصميم' }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $design->id }}</p>
                            @if($design->order_items_count > 0)
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-shopping-cart"></i>
                                {{ $design->order_items_count }} طلب
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($design->images && $design->images->count() > 0)
                            <img 
                                src="{{ $design->images->first()->image_url }}" 
                                alt="{{ $design->getTranslation('name', 'ar') }}"
                                class="w-16 h-16 rounded-lg object-cover ring-2 ring-gray-200"
                                onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center\'><i class=\'fas fa-palette text-2xl text-gray-300\'></i></div>';"
                            >
                        @else
                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-palette text-2xl text-gray-300"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-gray-400 text-sm"></i>
                            <span class="text-sm font-semibold text-indigo-600">{{ number_format($design->price, 2) }} ر.س</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            @forelse($design->sizes as $size)
                                <span class="px-2 py-1 bg-blue-100 text-blue-900 text-xs rounded-full font-semibold">{{ $size->code }}</span>
                            @empty
                                <span class="text-xs text-gray-400">لا يوجد</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($design->user->avatar)
                                <img src="{{ Storage::url($design->user->avatar) }}" alt="{{ $design->user->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ substr($design->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $design->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $design->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                {{ $design->is_active ? 'checked' : '' }}
                                class="sr-only peer"
                                onchange="toggleDesignStatus({{ $design->id }}, this.checked)"
                            >
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-green-600"></div>
                        </label>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $design->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs text-gray-500">{{ $design->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.designs.show', $design) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 hover:bg-blue-50 rounded-lg" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button 
                                onclick="deleteDesign({{ $design->id }})" 
                                class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 hover:bg-red-50 rounded-lg"
                                title="حذف"
                            >
                                <i class="fas fa-trash" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-palette text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-semibold mb-2 text-gray-700">لا توجد تصاميم</p>
                            <p class="text-sm text-gray-500">لم يتم العثور على أي تصاميم تطابق معايير البحث</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($designs->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $designs->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Ensure Font Awesome icons are always visible
(function() {
    const fixIcons = () => {
        document.querySelectorAll('i.fas, i.far, i.fal, i.fab').forEach(icon => {
            // Force icon visibility with !important
            icon.style.setProperty('font-family', 'Font Awesome 6 Free', 'important');
            icon.style.setProperty('font-weight', '900', 'important');
            icon.style.setProperty('display', 'inline-block', 'important');
            icon.style.setProperty('visibility', 'visible', 'important');
            icon.style.setProperty('opacity', '1', 'important');
            icon.style.setProperty('font-style', 'normal', 'important');
        });
    };
    
    // Run immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixIcons);
    } else {
        fixIcons();
    }
    
    // Run multiple times to catch any late-loading issues
    [100, 300, 500, 1000, 2000].forEach(delay => {
        setTimeout(fixIcons, delay);
    });
    
    // Use MutationObserver to watch for any changes that might hide icons
    if (window.MutationObserver) {
        const observer = new MutationObserver((mutations) => {
            let shouldFix = false;
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    shouldFix = true;
                }
            });
            if (shouldFix) {
                setTimeout(fixIcons, 50);
            }
        });
        
        // Observe all icon containers
        document.querySelectorAll('[class*="stat"], [class*="icon"]').forEach(container => {
            observer.observe(container, {
                attributes: true,
                attributeFilter: ['style', 'class'],
                subtree: true
            });
        });
    }
    
    // Watch for Font Awesome CSS to load
    const faStylesheet = document.querySelector('link[href*="font-awesome"]');
    if (faStylesheet) {
        faStylesheet.addEventListener('load', fixIcons);
        faStylesheet.addEventListener('error', () => {
            console.warn('Font Awesome failed to load');
        });
    }
})();

function toggleDesignStatus(designId, isActive) {
    fetch(`/dashboard/designs/${designId}/toggle-status`, {
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
            showNotification('success', data.message || 'تم تحديث حالة التصميم بنجاح');
        } else {
            event.target.checked = !isActive;
            showNotification('error', data.message || 'حدث خطأ أثناء تحديث حالة التصميم');
        }
    })
    .catch(error => {
        event.target.checked = !isActive;
        showNotification('error', 'حدث خطأ أثناء تحديث حالة التصميم');
        console.error('Error:', error);
    });
}

function deleteDesign(designId) {
    if (!confirm('هل أنت متأكد من حذف هذا التصميم؟ لا يمكن التراجع عن هذا الإجراء.')) {
        return;
    }

    fetch(`/dashboard/designs/${designId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'تم حذف التصميم بنجاح');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'حدث خطأ أثناء حذف التصميم');
        }
    })
    .catch(error => {
        showNotification('error', 'حدث خطأ أثناء حذف التصميم');
        console.error('Error:', error);
    });
}

function exportDesigns() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/dashboard/designs/export?${params.toString()}`;
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

