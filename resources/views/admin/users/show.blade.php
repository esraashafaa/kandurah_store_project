@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')

@push('styles')
<style>
    .user-profile-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%) !important;
        position: relative;
        overflow: hidden;
        animation: gradientShift 8s ease infinite;
        background-size: 200% 200%;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .user-profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.15) 0%, transparent 50%);
        pointer-events: none;
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 0.8; }
        50% { opacity: 1; }
    }
    
    .user-profile-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: float 25s infinite linear;
        pointer-events: none;
    }
    
    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-40px, -40px) rotate(360deg); }
    }
    
    .profile-avatar {
        position: relative;
        z-index: 10;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .profile-avatar:hover {
        transform: scale(1.08) rotate(2deg);
    }
    
    .profile-avatar img,
    .profile-avatar > div {
        transition: all 0.3s ease;
    }
    
    .avatar-status-indicator {
        animation: statusPulse 2s ease-in-out infinite;
    }
    
    @keyframes statusPulse {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        50% { 
            transform: scale(1.1);
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
        }
    }
    
    .profile-badge {
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }
    
    .profile-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }
    
    .profile-badge:hover {
        background: rgba(255, 255, 255, 0.28);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .profile-badge:hover::before {
        left: 100%;
    }
    
    .action-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .action-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .action-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    
    .user-info-section {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .badge-container {
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }
    
    .actions-container {
        animation: fadeInUp 1s ease-out 0.4s both;
    }
    
    @media (max-width: 1024px) {
        .user-profile-header {
            padding: 1.5rem !important;
        }
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
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ $user->name }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- User Profile Header -->
<div class="user-profile-header rounded-3xl shadow-2xl p-6 md:p-8 lg:p-10 mb-8 text-white relative overflow-hidden">
    
    <!-- Content Container -->
    <div class="relative z-10">
        <div class="flex flex-col xl:flex-row items-center xl:items-start gap-6 xl:gap-8">
            
            <!-- Avatar Section -->
            <div class="profile-avatar flex-shrink-0 order-1 xl:order-1">
                @if($user->avatar)
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full opacity-75 group-hover:opacity-100 blur transition duration-300"></div>
                        <img 
                            src="{{ Storage::url($user->avatar) }}" 
                            alt="{{ $user->name }}" 
                            class="relative w-32 h-32 md:w-40 md:h-40 lg:w-44 lg:h-44 rounded-full object-cover border-4 border-white shadow-2xl ring-4 ring-white ring-opacity-40"
                        >
                        <div class="absolute -bottom-1 -right-1 avatar-status-indicator w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full border-4 border-white flex items-center justify-center shadow-xl">
                            <i class="fas fa-check text-white text-xs md:text-sm"></i>
                        </div>
                    </div>
                @else
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full opacity-75 group-hover:opacity-100 blur transition duration-300"></div>
                        <div class="relative w-32 h-32 md:w-40 md:h-40 lg:w-44 lg:h-44 rounded-full bg-white bg-opacity-25 backdrop-blur-md flex items-center justify-center text-5xl md:text-6xl lg:text-7xl font-bold border-4 border-white shadow-2xl ring-4 ring-white ring-opacity-40">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- User Info Section -->
            <div class="flex-1 text-center xl:text-right w-full xl:w-auto order-2 xl:order-2 user-info-section">
                <!-- Name & Email -->
                <div class="mb-5 md:mb-6">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-3 drop-shadow-2xl tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <div class="flex items-center justify-center xl:justify-start gap-3 text-white text-opacity-95">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center">
                            <i class="fas fa-envelope text-sm md:text-base"></i>
                        </div>
                        <p class="text-base md:text-lg lg:text-xl font-medium">{{ $user->email }}</p>
                    </div>
                </div>
                
                <!-- Badges Section -->
                <div class="flex flex-wrap gap-3 justify-center xl:justify-start mb-5 md:mb-6 badge-container">
                    <!-- Role Badge -->
                    <span class="profile-badge inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm md:text-base font-semibold shadow-xl">
                        <i class="fas fa-user-tag text-sm"></i>
                        <span>{{ __('users.roles.' . $user->role->value) }}</span>
                    </span>
                    
                    <!-- Status Badge -->
                    @if($user->is_active)
                        <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 bg-opacity-95 backdrop-blur-sm rounded-full text-sm md:text-base font-semibold shadow-xl border-2 border-green-300 border-opacity-60 hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-check-circle animate-pulse"></i>
                            <span>نشط</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 bg-opacity-95 backdrop-blur-sm rounded-full text-sm md:text-base font-semibold shadow-xl border-2 border-red-300 border-opacity-60 hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-times-circle"></i>
                            <span>غير نشط</span>
                        </span>
                    @endif

                    <!-- Email Verified Badge -->
                    @if($user->email_verified_at)
                        <span class="profile-badge inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm md:text-base font-semibold shadow-xl">
                            <i class="fas fa-shield-check text-green-300"></i>
                            <span>تم التحقق من البريد</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Actions Section -->
            <div class="flex flex-col sm:flex-row xl:flex-col gap-3 w-full xl:w-auto xl:flex-shrink-0 order-3 xl:order-3 actions-container">
                <a 
                    href="{{ route('admin.users.edit', $user) }}" 
                    class="action-btn bg-white text-indigo-600 px-6 py-3.5 rounded-xl hover:bg-gray-50 inline-flex items-center justify-center gap-2.5 font-semibold shadow-xl text-sm md:text-base relative overflow-hidden"
                >
                    <i class="fas fa-edit text-base"></i>
                    <span>تعديل</span>
                </a>
                <a 
                    href="{{ route('admin.users.index') }}" 
                    class="action-btn bg-white text-indigo-600 px-6 py-3.5 rounded-xl hover:bg-gray-50 inline-flex items-center justify-center gap-2.5 font-semibold shadow-xl text-sm md:text-base relative overflow-hidden"
                >
                    <i class="fas fa-arrow-right text-base"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">الطلبات</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->orders()->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">التصاميم</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->designs()->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-palette text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">رصيد المحفظة</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($user->wallet_balance ?? 0, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">العناوين</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->locations()->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">آخر الطلبات</h2>
                    <a href="{{ route('dashboard.orders.index', ['user_id' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($user->orders()->latest()->take(5)->get() as $order)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-semibold text-gray-900">طلب #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} ريال</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status->value === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status->value === 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                    <p>لا توجد طلبات بعد</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Designs -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">آخر التصاميم</h2>
                    <a href="{{ route('dashboard.designs.index', ['user_id' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4">
                @forelse($user->designs()->latest()->take(6)->get() as $design)
                <div class="group relative aspect-square rounded-lg overflow-hidden bg-gray-100">
                    @if($design->images->first())
                        <img src="{{ Storage::url($design->images->first()->image_path) }}" alt="{{ $design->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-palette text-4xl text-gray-300"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            <p class="text-white text-sm font-semibold">{{ $design->name }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full p-8 text-center text-gray-500">
                    <i class="fas fa-palette text-4xl mb-2"></i>
                    <p>لا توجد تصاميم بعد</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        
        <!-- User Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">معلومات المستخدم</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">الاسم الكامل</p>
                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                    <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">رقم الهاتف</p>
                    <p class="font-semibold text-gray-900">{{ $user->phone ?? 'غير محدد' }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">تاريخ التسجيل</p>
                    <p class="font-semibold text-gray-900">{{ $user->created_at->format('Y-m-d') }}</p>
                    <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                @if($user->email_verified_at)
                <hr>
                <div>
                    <p class="text-sm text-gray-600">تاريخ التحقق من البريد</p>
                    <p class="font-semibold text-gray-900">{{ $user->email_verified_at->format('Y-m-d') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Wallet Transactions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">المحفظة</h2>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm" onclick="showAddBalanceModal()">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة رصيد
                </button>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-4 text-white mb-4">
                <p class="text-sm opacity-90 mb-1">الرصيد الحالي</p>
                <p class="text-3xl font-bold">{{ number_format($user->wallet_balance ?? 0, 2) }} ريال</p>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($user->transactions()->latest()->take(10)->get() as $transaction)
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            {{ $transaction->type === 'deposit' ? 'bg-green-100' : 'bg-red-100' }}
                        ">
                            <i class="fas {{ $transaction->type === 'deposit' ? 'fa-plus text-green-600' : 'fa-minus text-red-600' }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('transactions.types.' . $transaction->type) }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    <p class="font-bold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                    </p>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-wallet text-2xl mb-2"></i>
                    <p class="text-sm">لا توجد معاملات</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">إجراءات سريعة</h2>
            <div class="space-y-2">
                <button onclick="sendNotification({{ $user->id }})" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas fa-bell text-indigo-600"></i>
                    <span class="text-gray-700">إرسال إشعار</span>
                </button>
                <button onclick="resetPassword({{ $user->id }})" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas fa-key text-orange-600"></i>
                    <span class="text-gray-700">إعادة تعيين كلمة المرور</span>
                </button>
                @if(Auth::id() !== $user->id)
                <button onclick="toggleUserStatus({{ $user->id }})" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas {{ $user->is_active ? 'fa-ban text-red-600' : 'fa-check text-green-600' }}"></i>
                    <span class="text-gray-700">{{ $user->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}</span>
                </button>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

<!-- Add Balance Modal -->
<div id="addBalanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">إضافة رصيد</h3>
            <button onclick="closeAddBalanceModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addBalanceForm" onsubmit="addBalance(event)">
            <div class="mb-4">
                <label for="balance_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    المبلغ (ريال) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="balance_amount" 
                    name="amount" 
                    step="0.01" 
                    min="0.01" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="0.00"
                >
            </div>
            
            <div class="mb-4">
                <label for="balance_description" class="block text-sm font-medium text-gray-700 mb-2">
                    الوصف (اختياري)
                </label>
                <textarea 
                    id="balance_description" 
                    name="description" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="وصف العملية..."
                ></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeAddBalanceModal()" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition"
                >
                    إلغاء
                </button>
                <button 
                    type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition"
                >
                    <i class="fas fa-plus ml-2"></i>
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showAddBalanceModal() {
    document.getElementById('addBalanceModal').classList.remove('hidden');
    document.getElementById('addBalanceModal').classList.add('flex');
}

function closeAddBalanceModal() {
    document.getElementById('addBalanceModal').classList.add('hidden');
    document.getElementById('addBalanceModal').classList.remove('flex');
    document.getElementById('addBalanceForm').reset();
}

function addBalance(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = {
        amount: parseFloat(formData.get('amount')),
        description: formData.get('description') || 'إضافة رصيد من قبل المشرف'
    };
    
    fetch('{{ route("admin.users.add-balance", $user) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم إضافة الرصيد بنجاح');
            closeAddBalanceModal();
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء إضافة الرصيد');
        console.error('Error:', error);
    });
}

function sendNotification(userId) {
    const message = prompt('أدخل رسالة الإشعار:');
    if (!message) return;
    
    // TODO: تنفيذ إرسال الإشعار
    alert('ميزة إرسال الإشعارات قيد التطوير');
}

function resetPassword(userId) {
    if (!confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟ سيتم إرسال رابط إعادة التعيين إلى بريد المستخدم.')) {
        return;
    }
    
    fetch('{{ route("admin.users.reset-password", $user) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء إرسال رابط إعادة التعيين');
        console.error('Error:', error);
    });
}

function toggleUserStatus(userId) {
    if (!confirm('هل أنت متأكد من تغيير حالة المستخدم؟')) {
        return;
    }
    
    fetch('{{ route("admin.users.toggle-status", $user) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء تحديث حالة المستخدم');
        console.error('Error:', error);
    });
}
</script>
@endpush

