<!-- Sidebar Overlay (Mobile) -->
<div 
    x-show="sidebarOpen && isMobile" 
    @click="sidebarOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="sidebar-overlay lg:hidden"
></div>

<!-- Sidebar Container -->
<div 
    class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 lg:z-0 lg:relative lg:inset-0 flex"
>
    <!-- Main Sidebar -->
    <aside 
        :class="(sidebarOpen || !isMobile) ? 'translate-x-0' : ('{{ app()->getLocale() }}' === 'ar' ? 'translate-x-full' : '-translate-x-full')"
        class="w-64 bg-white shadow-2xl lg:shadow-none transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:transform-none flex-shrink-0 h-screen flex flex-col {{ app()->getLocale() === 'ar' ? 'border-l' : 'border-r' }} border-gray-200"
    >
    <!-- Logo -->
    <div class="flex-shrink-0 flex items-center justify-center h-20 bg-gradient-to-{{ app()->getLocale() === 'ar' ? 'l' : 'r' }} from-indigo-600 via-purple-600 to-pink-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-{{ app()->getLocale() === 'ar' ? 'l' : 'r' }} from-indigo-600 via-purple-600 to-pink-600 opacity-50 animate-pulse"></div>
        <h1 class="text-2xl font-bold text-white flex items-center gap-3 relative z-10">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-store text-2xl"></i>
            </div>
            {{ __('sidebar.store_name') }}
        </h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 min-h-0 mt-6 px-2 pb-6 overflow-y-auto">
        @php
            // الحصول على Admin من guard admin أو guard الافتراضي
            $admin = auth()->guard('admin')->user() ?? (auth()->user() instanceof \App\Models\Admin ? auth()->user() : null);
            // التحقق من Role من Spatie Permissions
            $isSuperAdmin = false;
            if ($admin && $admin instanceof \App\Models\Admin) {
                // التحقق من Spatie Permission Role أولاً
                // guard_name محدد في Admin model كـ 'web'
                $isSuperAdmin = $admin->hasRole('super-admin');
                
                // إذا لم يكن لديه الدور من Spatie، نتحقق من حقل role في الجدول
                if (!$isSuperAdmin) {
                    $roleValue = $admin->role instanceof \App\Enums\RoleEnum ? $admin->role->value : $admin->role;
                    $isSuperAdmin = ($roleValue === 'super_admin');
                }
            }
        @endphp
        
        <!-- الرئيسية -->
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>{{ __('sidebar.home') }}</span>
        </a>

        <!-- المستخدمين -->
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>{{ __('sidebar.users') }}</span>
        </a>

        <!-- المشرفين (فقط للسوبر أدمن) -->
        @if($isSuperAdmin)
        <a href="{{ route('admin.admins.index') }}" class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i>
            <span>{{ __('sidebar.admins') }}</span>
        </a>
        @endif

        <!-- الطلبات -->
        <a href="{{ route('dashboard.orders.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.orders.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>{{ __('sidebar.orders') }}</span>
        </a>

        <!-- التصاميم -->
        <a href="{{ route('dashboard.designs.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.designs.*') ? 'active' : '' }}">
            <i class="fas fa-palette"></i>
            <span>{{ __('sidebar.designs') }}</span>
        </a>

        <!-- خيارات التصميم -->
        <a href="{{ route('dashboard.design-options.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.design-options.*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i>
            <span>{{ __('sidebar.design_options') }}</span>
        </a>

        <!-- المواقع -->
        <a href="{{ route('dashboard.locations.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.locations.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ __('sidebar.locations') }}</span>
        </a>

        <!-- الكوبونات -->
        <a href="{{ route('admin.coupons.index') }}" class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>{{ __('sidebar.coupons') }}</span>
        </a>

        <!-- التقييمات -->
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>{{ __('sidebar.reviews') }}</span>
        </a>

        <hr class="my-4 border-gray-200">

        <!-- مجموعات الصلاحيات (فقط للسوبر أدمن) -->
        @if($isSuperAdmin)
        <a href="{{ route('admin.permission-groups.index') }}" class="sidebar-link {{ request()->routeIs('admin.permission-groups.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>{{ __('admin.permission_groups.title') }}</span>
        </a>
        @endif

    </nav>
    </aside>
 </div>

