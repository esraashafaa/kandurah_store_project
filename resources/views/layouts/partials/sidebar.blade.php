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
    class="fixed inset-y-0 right-0 z-50 lg:z-0 flex"
>
    <!-- Main Sidebar -->
    <aside 
        :class="(sidebarOpen || !isMobile) ? 'translate-x-0' : 'translate-x-full'"
        class="w-64 bg-white shadow-2xl lg:shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:transform-none flex-shrink-0 h-screen overflow-hidden"
    >
    <!-- Logo -->
    <div class="flex items-center justify-center h-20 bg-gradient-to-l from-indigo-600 via-purple-600 to-pink-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-l from-indigo-600 via-purple-600 to-pink-600 opacity-50 animate-pulse"></div>
        <h1 class="text-2xl font-bold text-white flex items-center gap-3 relative z-10">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-store text-2xl"></i>
            </div>
            متجر كندرة
        </h1>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-2 overflow-hidden pb-6">
        
        <!-- الرئيسية -->
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>

        <!-- المستخدمين -->
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>المستخدمين</span>
        </a>

        <!-- الطلبات -->
        <a href="{{ route('dashboard.orders.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.orders.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>الطلبات</span>
        </a>

        <!-- التصاميم -->
        <a href="{{ route('dashboard.designs.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.designs.*') ? 'active' : '' }}">
            <i class="fas fa-palette"></i>
            <span>التصاميم</span>
        </a>

        <!-- خيارات التصميم -->
        <a href="{{ route('dashboard.design-options.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.design-options.*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i>
            <span>خيارات التصميم</span>
        </a>

        <!-- المواقع -->
        <a href="{{ route('dashboard.locations.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.locations.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>المواقع</span>
        </a>

        <!-- الكوبونات -->
        <a href="{{ route('admin.coupons.index') }}" class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>الكوبونات</span>
        </a>

        <!-- التقييمات -->
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>التقييمات</span>
        </a>

        <!-- الإشعارات -->
        <a href="{{ route('admin.notifications.index') }}" class="sidebar-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            <span>الإشعارات</span>
        </a>

        <hr class="my-4 border-gray-200">

        <!-- الإعدادات -->
        @if(auth()->user()->role === 'super_admin')
        <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>الإعدادات</span>
        </a>
        @endif

    </nav>
    </aside>
</div>

