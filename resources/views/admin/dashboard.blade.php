@extends('layouts.admin')

@section('title', __('dashboard.title'))

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .slide-in-right {
        animation: slideInRight 0.6s ease-out forwards;
    }
    
    .delay-1 { animation-delay: 0.1s; opacity: 0; }
    .delay-2 { animation-delay: 0.2s; opacity: 0; }
    .delay-3 { animation-delay: 0.3s; opacity: 0; }
    .delay-4 { animation-delay: 0.4s; opacity: 0; }
    .delay-5 { animation-delay: 0.5s; opacity: 0; }
    
    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: shimmer 20s linear infinite;
    }
    
    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--card-gradient));
        border-radius: 0 0 0 100px;
        opacity: 0.1;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }
    
    .stat-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    
    .stat-card-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .stat-card-change {
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    
    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    /* Order Item */
    .order-item {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }
    
    .order-item:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        transform: translateX(-4px);
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Quick Action Card */
    .quick-action-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
        cursor: pointer;
    }
    
    .quick-action-card:hover {
        border-color: #6366f1;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
    }
    
    .quick-action-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 12px;
        color: white;
    }
    
    /* Progress Bar */
    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 4px;
        transition: width 0.6s ease;
    }
</style>
@endpush

@section('content')

<!-- Welcome Section -->
<div class="welcome-section rounded-3xl p-8 mb-8 text-white relative fade-in-up">
    <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl lg:text-5xl font-bold mb-3 flex items-center gap-3">
                <span>👋</span>
                {{ __('dashboard.welcome_user', ['name' => Auth::user()->name]) }}
            </h1>
            <p class="text-lg lg:text-xl text-white/90 flex items-center gap-2">
                <i class="fas fa-calendar-day"></i>
                {{ __('dashboard.overview') }} - {{ now()->format('d/m/Y') }}
            </p>
        </div>
        <div class="hidden lg:block">
            <div class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center border-2 border-white/30">
                <i class="fas fa-chart-line text-6xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Statistics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- إجمالي المستخدمين -->
    <div class="stat-card fade-in-up delay-1" style="--card-gradient: #3b82f6, #2563eb">
        <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-card-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
        <div class="stat-card-label">{{ __('dashboard.stats.total_users') }}</div>
        <div class="stat-card-change" style="color: #10b981;">
            <i class="fas fa-arrow-up"></i>
            <span>{{ $stats['new_users_today'] ?? 0 }} {{ __('dashboard.stats.new_today') }}</span>
        </div>
    </div>

    <!-- إجمالي الطلبات -->
    <div class="stat-card fade-in-up delay-2" style="--card-gradient: #8b5cf6, #7c3aed">
        <div class="stat-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-card-value">{{ number_format($stats['total_orders'] ?? 0) }}</div>
        <div class="stat-card-label">{{ __('dashboard.stats.total_orders') }}</div>
        <div class="stat-card-change" style="color: #f59e0b;">
            <i class="fas fa-clock"></i>
            <span>{{ $stats['pending_orders'] ?? 0 }} {{ __('dashboard.stats.pending') }}</span>
        </div>
    </div>

    <!-- إجمالي الإيرادات -->
    <div class="stat-card fade-in-up delay-3" style="--card-gradient: #10b981, #059669">
        <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-card-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
        <div class="stat-card-label">{{ __('dashboard.stats.total_revenue_currency') }}</div>
        <div class="stat-card-change" style="color: #10b981;">
            <i class="fas fa-calendar-day"></i>
            <span>{{ number_format($stats['revenue_today'] ?? 0, 2) }} {{ __('dashboard.stats.revenue_today') }}</span>
        </div>
    </div>

    <!-- إجمالي التصاميم -->
    <div class="stat-card fade-in-up delay-4" style="--card-gradient: #f59e0b, #d97706">
        <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i class="fas fa-palette"></i>
        </div>
        <div class="stat-card-value">{{ number_format($stats['total_designs'] ?? 0) }}</div>
        <div class="stat-card-label">{{ __('dashboard.stats.total_designs') }}</div>
        <div class="stat-card-change" style="color: #10b981;">
            <i class="fas fa-plus-circle"></i>
            <span>{{ $stats['new_designs_today'] ?? 0 }} {{ __('dashboard.stats.new_today') }}</span>
        </div>
    </div>

</div>

<!-- Secondary Statistics & Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Coupons Statistics -->
    <div class="chart-container fade-in-up delay-1">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tags text-indigo-600"></i>
                {{ __('dashboard.coupons.title') }}
            </h3>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.coupons.active') }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['active_coupons'] ?? 0) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total_coupons'] > 0 ? ($stats['active_coupons'] / $stats['total_coupons'] * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.coupons.expired') }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['expired_coupons'] ?? 0) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total_coupons'] > 0 ? ($stats['expired_coupons'] / $stats['total_coupons'] * 100) : 0 }}%; background: linear-gradient(90deg, #ef4444, #dc2626);"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.coupons.used') }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['used_coupons'] ?? 0) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total_coupons'] > 0 ? ($stats['used_coupons'] / $stats['total_coupons'] * 100) : 0 }}%; background: linear-gradient(90deg, #ec4899, #db2777);"></div>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold text-gray-900">{{ __('dashboard.coupons.total') }}</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_coupons'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Status -->
    <div class="chart-container fade-in-up delay-2">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-600"></i>
                {{ __('dashboard.orders.title') }}
            </h3>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.orders.pending') }}</span>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ $stats['pending_orders'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.orders.processing') }}</span>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ $stats['processing_orders'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.orders.completed') }}</span>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ $stats['completed_orders'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('dashboard.orders.cancelled') }}</span>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ $stats['cancelled_orders'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="chart-container fade-in-up delay-3">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-600"></i>
                {{ __('dashboard.quick_actions.title') }}
            </h3>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.coupons.create') }}" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <i class="fas fa-tag"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('dashboard.quick_actions.add_coupon') }}</p>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('dashboard.quick_actions.orders') }}</p>
            </a>
            <a href="{{ route('admin.users.index') }}" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-users"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('dashboard.quick_actions.users') }}</p>
            </a>
            <a href="{{ route('admin.designs.index') }}" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-palette"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('dashboard.quick_actions.designs') }}</p>
            </a>
        </div>
    </div>

</div>

<!-- Recent Orders Section -->
<div class="chart-container fade-in-up delay-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-clock text-purple-600"></i>
            {{ __('dashboard.orders.recent_title') }}
        </h2>
        <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold flex items-center gap-2 transition-colors">
            {{ __('dashboard.orders.view_all') }}
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    
    @if($recent_orders->count() > 0)
    <div class="space-y-3">
        @foreach($recent_orders as $order)
        <a href="{{ route('admin.orders.show', $order->id) }}" class="order-item block">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        #{{ $order->id }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 mb-1">{{ __('dashboard.orders.order_number', ['id' => $order->id]) }}</p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-user text-xs"></i>
                            {{ $order->user->name ?? __('dashboard.orders.deleted_user') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                            <i class="fas fa-calendar text-xs"></i>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                <div class="text-left">
                    <p class="font-bold text-gray-900 text-lg mb-2">{{ number_format($order->total_amount, 2) }} {{ __('dashboard.common.sar') }}</p>
                    <span class="status-badge 
                        @if($order->status->value === 'pending') bg-amber-100 text-amber-700
                        @elseif($order->status->value === 'processing') bg-blue-100 text-blue-700
                        @elseif($order->status->value === 'delivered') bg-green-100 text-green-700
                        @elseif($order->status->value === 'cancelled') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        <i class="fas fa-circle text-xs"></i>
                        {{ $order->status->label() }}
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
        </div>
        <p class="text-gray-500 text-lg font-medium">{{ __('dashboard.orders.no_recent_orders') }}</p>
        <p class="text-gray-400 text-sm mt-2">{{ __('dashboard.orders.no_recent_orders_desc') }}</p>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // Add smooth scroll animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
</script>
@endpush
