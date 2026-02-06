<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('dashboard.title')) - {{ config('app.name', 'Kandura Store') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        /* Dynamic Sidebar Link Gradient based on Language */
        .sidebar-link.active {
            background: linear-gradient(to {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}, #4f46e5, #7c3aed, #ec4899) !important;
        }
        
        .sidebar-link.active:hover {
            background: linear-gradient(to {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}, #4338ca, #6d28d9, #db2777) !important;
        }
        
        /* Dynamic Border for Submenu Items */
        .submenu-item.active {
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}-width: 4px !important;
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}-color: #4f46e5 !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div 
        x-data="{
            sidebarOpen: false,
            isMobile: window.innerWidth < 1024
        }"
        @resize.window="isMobile = window.innerWidth < 1024"
        class="flex h-screen overflow-hidden bg-gray-100 lg:bg-white"
    >
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden lg:border-l {{ app()->getLocale() === 'ar' ? 'lg:border-r lg:border-l-0' : 'lg:border-l' }} border-gray-200 bg-white">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 z-30 shrink-0">
                <!-- شريط علوي زخرفي -->
                <div class="h-1 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600"></div>
                <div class="flex items-center justify-between gap-4 px-4 py-3 sm:px-6 min-h-[3.5rem]">
                    <!-- يسار: زر القائمة (موبايل) + عنوان الصفحة (موبايل) -->
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden shrink-0 p-2.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                            aria-label="{{ __('common.menu') ?? 'القائمة' }}"
                        >
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="lg:hidden text-base font-semibold text-gray-800 truncate">
                            @yield('title', __('dashboard.title'))
                        </h1>
                    </div>

                    <!-- يمين: جرس الإشعارات ثم الدائرة (الحساب) ثم مبدّل اللغة -->
                    @php
                        $headerUser = Auth::guard('admin')->user() ?? Auth::user();
                    @endphp
                    <div class="flex items-center gap-3 sm:gap-4 shrink-0 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                        @include('layouts.partials.notifications-bell')
                        @if($headerUser)
                        <!-- المستخدم: الدائرة تفتح قائمة معلومات الحساب (تظهر أولاً) -->
                        <div class="relative shrink-0 min-w-[2.25rem] sm:min-w-[2.5rem]" x-data="{ accountMenuOpen: false }" @click.outside="accountMenuOpen = false">
                            <button type="button"
                                    @click="accountMenuOpen = !accountMenuOpen"
                                    class="inline-flex items-center gap-2 sm:gap-3 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    :aria-expanded="accountMenuOpen">
                                <!-- الدائرة (الأفاتار) - شكل دائري ثابت -->
                                <span class="inline-flex shrink-0 w-10 h-10 items-center justify-center rounded-full overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white text-sm font-bold shadow-md ring-2 ring-white aspect-square box-border" style="min-width: 2.5rem; min-height: 2.5rem;">
                                    {{ strtoupper(mb_substr($headerUser->name ?? 'A', 0, 1)) }}
                                </span>
                                <span class="hidden sm:block text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} min-w-0 truncate">
                                    <span class="block text-sm font-medium text-gray-900 truncate max-w-[140px]">{{ $headerUser->name }}</span>
                                    <span class="block text-xs text-gray-500 truncate max-w-[140px]">{{ $headerUser->email ?? '' }}</span>
                                </span>
                            </button>

                            <!-- قائمة معلومات الحساب -->
                            <div x-show="accountMenuOpen"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-50 mt-2 w-64 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-2 {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }}"
                                 style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $headerUser->name }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $headerUser->email ?? '' }}</p>
                                </div>
                                <div class="py-1">
                                    @if(Route::has('profile.edit'))
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                        <i class="fas fa-user w-4 text-gray-400"></i>
                                        <span>{{ __('profile.edit_profile') ?? 'الملف الشخصي' }}</span>
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                            <i class="fas fa-sign-out-alt w-4"></i>
                                            <span>{{ __('auth.logout') ?? 'تسجيل الخروج' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:block w-px h-8 bg-gray-200 shrink-0" aria-hidden="true"></div>
                        @endif

                        <!-- مبدّل اللغة -->
                        <div class="flex rounded-lg border border-gray-200 bg-gray-50/80 p-0.5 shrink-0" role="group">
                            <a href="{{ route('language.switch', 'ar') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white hover:text-gray-900' }}"
                               title="{{ __('translation.arabic') ?? 'العربية' }}">
                                عربي
                            </a>
                            <a href="{{ route('language.switch', 'en') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white hover:text-gray-900' }}"
                               title="{{ __('translation.english') ?? 'English' }}">
                                EN
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-gray-50">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>حدثت الأخطاء التالية:</strong>
                        </div>
                        <ul class="list-disc list-inside mr-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

