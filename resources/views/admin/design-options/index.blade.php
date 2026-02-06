@extends('layouts.admin')

@section('title', __('admin.design_options.title'))

@push('styles')
<style>
    .search-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .search-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
    .create-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .create-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.design_options.title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('admin.design_options.subtitle') }}</p>
    </div>
    @can('create', App\Models\DesignOption::class)
        <a href="{{ route('dashboard.design-options.create') }}" class="create-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
            <i class="fas fa-plus"></i>
            <span>{{ __('admin.design_options.add_new') }}</span>
        </a>
    @endcan
</div>

<!-- Statistics Cards -->
<div class="w-full flex gap-4 mb-8 flex-nowrap items-stretch">
    
    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-blue-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-sliders-h text-blue-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">{{ __('admin.design_options.total_options') }}</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-green-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">{{ __('admin.design_options.active') }}</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-purple-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-layer-group text-purple-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">{{ __('admin.design_options.option_types') }}</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['types'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-orange-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-chart-line text-orange-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">{{ __('admin.design_options.used') }}</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['used'] ?? 0 }}</p>
        </div>
    </div>

</div>

<!-- Filters & Search - Single Line -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="GET" action="{{ route('dashboard.design-options.index') }}" class="flex flex-col lg:flex-row gap-4">
        
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="{{ __('admin.design_options.search_placeholder') }}"
                    class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >
                <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
            </div>
        </div>

        <!-- Type Filter -->
        <div class="w-full lg:w-48">
            <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">{{ __('admin.design_options.all_types') }}</option>
                <option value="collar" {{ request('type') === 'collar' ? 'selected' : '' }}>{{ __('designs.option_types.collar') }}</option>
                <option value="sleeve" {{ request('type') === 'sleeve' ? 'selected' : '' }}>{{ __('designs.option_types.sleeve') }}</option>
                <option value="pocket" {{ request('type') === 'pocket' ? 'selected' : '' }}>{{ __('designs.option_types.pocket') }}</option>
                <option value="button" {{ request('type') === 'button' ? 'selected' : '' }}>{{ __('designs.option_types.button') }}</option>
                <option value="color" {{ request('type') === 'color' ? 'selected' : '' }}>{{ __('designs.option_types.color') }}</option>
                <option value="dome_type" {{ request('type') === 'dome_type' ? 'selected' : '' }}>{{ __('designs.option_types.dome_type') }}</option>
                <option value="fabric_type" {{ request('type') === 'fabric_type' ? 'selected' : '' }}>{{ __('designs.option_types.fabric_type') }}</option>
                <option value="sleeve_type" {{ request('type') === 'sleeve_type' ? 'selected' : '' }}>{{ __('designs.option_types.sleeve_type') }}</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full lg:w-48">
            <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                <option value="">{{ __('admin.design_options.all_statuses') }}</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ __('common.active') }}</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ __('common.inactive') }}</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="search-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                <i class="fas fa-filter"></i>
                <span class="hidden sm:inline">{{ __('common.search') }}</span>
            </button>
            <a href="{{ route('dashboard.design-options.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
                <i class="fas fa-redo"></i>
                <span class="hidden sm:inline">{{ __('common.reset') }}</span>
            </a>
        </div>
    </form>
</div>

<!-- Options Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.option') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.type') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.status') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.designs_count') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.created_at') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.design_options.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($options as $option)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($option->image_path)
                                <img src="{{ Storage::url($option->image_path) }}" alt="{{ $option->getTranslation('name', app()->getLocale(), true) ?: $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                    {{ mb_substr($option->getTranslation('name', app()->getLocale(), true) ?: $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true), 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $option->getTranslation('name', app()->getLocale(), true) ?: $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}</p>
                                <p class="text-xs text-gray-500">{{ $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-900">
                            <i class="fas fa-tag"></i>
                            @if(isset(__('designs.option_types')[$option->type->value]))
                                {{ __('designs.option_types.' . $option->type->value) }}
                            @else
                                {{ $option->type->labelAr() }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                {{ $option->is_active ? 'checked' : '' }}
                                class="sr-only peer"
                                onchange="toggleOptionStatus({{ $option->id }}, this.checked)"
                            >
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-green-600"></div>
                        </label>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-palette text-gray-400 text-sm"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ $option->designs()->count() }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $option->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs text-gray-500">{{ $option->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.design-options.show', $option) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 hover:bg-blue-50 rounded-lg" title="{{ __('common.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('update', $option)
                                <a href="{{ route('dashboard.design-options.edit', $option) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200 p-2 hover:bg-indigo-50 rounded-lg" title="{{ __('common.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('delete', $option)
                                <button 
                                    onclick="deleteOption({{ $option->id }})" 
                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 hover:bg-red-50 rounded-lg"
                                    title="{{ __('common.delete') }}"
                                >
                                    <i class="fas fa-trash" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-sliders-h text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-semibold mb-2 text-gray-700">{{ __('admin.design_options.no_options') }}</p>
                            <p class="text-sm text-gray-500">{{ __('admin.design_options.no_options_desc') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($options->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $options->links() }}
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

function toggleOptionStatus(optionId, isActive) {
    if (!confirm('{{ __('admin.design_options.toggle_confirm') }}')) {
        event.target.checked = !isActive;
        return;
    }

    fetch(`/dashboard/design-options/${optionId}/toggle`, {
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
            showNotification('success', data.message || '{{ __('admin.design_options.toggle_success') }}');
        } else {
            event.target.checked = !isActive;
            showNotification('error', data.message || '{{ __('admin.design_options.toggle_error') }}');
        }
    })
    .catch(error => {
        event.target.checked = !isActive;
        showNotification('error', '{{ __('admin.design_options.toggle_error') }}');
        console.error('Error:', error);
    });
}

function deleteOption(optionId) {
    if (!confirm('{{ __('admin.design_options.delete_confirm') }}')) {
        return;
    }

    fetch(`/dashboard/design-options/${optionId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // التحقق من نوع المحتوى
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // إذا كانت الاستجابة HTML، يعني هناك خطأ في الـ route أو الصلاحيات
            return response.text().then(html => {
                console.error('Received HTML instead of JSON:', html);
                throw new Error('حدث خطأ في الاتصال بالخادم. يرجى التحقق من الصلاحيات.');
            });
        }
        
        // التحقق من حالة الاستجابة
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || data.message_ar || 'حدث خطأ أثناء حذف الخيار');
            }).catch(err => {
                // إذا فشل parsing JSON، يعني الاستجابة HTML
                if (err instanceof SyntaxError) {
                    throw new Error('حدث خطأ في الاتصال بالخادم. يرجى التحقق من الصلاحيات.');
                }
                throw err;
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const message = data.message_ar || data.message || '{{ __('admin.design_options.delete_success') }}';
            showNotification('success', message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            const message = data.message_ar || data.message || '{{ __('admin.design_options.delete_error') }}';
            showNotification('error', message);
        }
    })
    .catch(error => {
        const errorMessage = error.message || '{{ __('admin.design_options.delete_error') }}';
        showNotification('error', errorMessage);
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
