@extends('layouts.admin')

@section('title', __('orders.order_details'))

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        nav, .breadcrumb {
            display: none !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
        .bg-gradient-to-r {
            background: #f9fafb !important;
        }
        button, a[href] {
            display: none !important;
        }
        .hover\:bg-gray-50 {
            background: transparent !important;
        }
        table {
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #d1d5db !important;
            padding: 8px !important;
        }
        @page {
            margin: 1cm;
        }
    }
</style>
@endpush

@section('content')

<!-- Breadcrumb -->
<nav class="flex mb-6 no-print" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="fas fa-home ml-2"></i>
                {{ __('sidebar.home') }}
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2 text-xs"></i>
                <a href="{{ route('dashboard.orders.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">{{ __('sidebar.orders') }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2 text-xs"></i>
                <span class="text-gray-500 font-medium">{{ __('orders.order_details') }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Order Header -->
<div class="bg-gradient-to-r from-gray-50 via-indigo-50 to-purple-50 rounded-2xl shadow-xl p-8 mb-8 border-2 border-indigo-200 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-indigo-100/20 to-purple-100/20"></div>
    <div class="relative z-10">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-4 mb-3">
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('orders.order_details') }}</h1>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'paid' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'confirmed' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'processing' => 'bg-purple-100 text-purple-800 border-purple-300',
                            'shipped' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
                            'delivered' => 'bg-green-100 text-green-800 border-green-300',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                        ];
                        $statusColor = $statusColors[$order->status->value] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                    @endphp
                    <span class="px-4 py-2 {{ $statusColor }} rounded-full text-sm font-semibold border-2 shadow-sm">
                        <i class="fas fa-circle text-xs ml-1"></i>
                        {{ $order->status->label() }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-6 text-gray-700">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                        <span class="font-medium">{{ __('orders.created_at') }}: <span class="text-gray-900">{{ $order->created_at->format('Y-m-d H:i') }}</span></span>
                    </div>
                    @if($order->updated_at != $order->created_at)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-indigo-600"></i>
                        <span class="font-medium">{{ __('orders.updated_at') }}: <span class="text-gray-900">{{ $order->updated_at->format('Y-m-d H:i') }}</span></span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if($order->invoice)
                <a href="{{ route('dashboard.orders.invoice.view', $order) }}" target="_blank" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-all duration-200 inline-flex items-center gap-2 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 no-print">
                    <i class="fas fa-file-pdf"></i>
                    <span>{{ __('orders.view_invoice_pdf') }}</span>
                </a>
                <a href="{{ route('dashboard.orders.invoice.download', $order) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-all duration-200 inline-flex items-center gap-2 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 no-print">
                    <i class="fas fa-download"></i>
                    <span>{{ __('orders.download_invoice') }}</span>
                </a>
                <button onclick="printInvoice()" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 inline-flex items-center gap-2 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 no-print">
                    <i class="fas fa-print"></i>
                    <span>{{ __('orders.print_invoice') }}</span>
                </button>
                @else
                <span class="bg-gray-300 text-gray-600 px-6 py-3 rounded-xl inline-flex items-center gap-2 font-semibold cursor-not-allowed no-print" title="{{ __('orders.invoice_available_after_completion') }}">
                    <i class="fas fa-file-pdf"></i>
                    <span>{{ __('orders.invoice_not_available') }}</span>
                </span>
                @endif
                @if($order->status->value !== 'delivered' && $order->status->value !== 'cancelled')
                <button onclick="showStatusModal()" class="bg-violet-100 text-violet-700 px-6 py-3 rounded-xl hover:bg-violet-200 transition-all duration-200 inline-flex items-center gap-2 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 border-2 border-violet-300 no-print">
                    <i class="fas fa-edit"></i>
                    <span>{{ __('orders.update_status') }}</span>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-shopping-bag text-indigo-600"></i>
                    {{ __('orders.order_items') }}
                </h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <div class="p-6 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200">
                    <div class="flex flex-col md:flex-row items-start gap-6">
                        @if($item->design)
                            <img src="{{ $item->design->display_image_url }}" alt="{{ is_array($item->design->name) ? ($item->design->name[app()->getLocale()] ?? $item->design->name['ar'] ?? $item->design->name['en'] ?? '') : $item->design->name }}" class="w-32 h-32 md:w-24 md:h-24 rounded-xl object-cover shadow-md ring-2 ring-gray-100" onerror="this.src='{{ asset(\App\Models\Design::PLACEHOLDER_IMAGE_PATH) }}'">
                        @else
                            <div class="w-32 h-32 md:w-24 md:h-24 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center shadow-md">
                                <i class="fas fa-palette text-indigo-400 text-3xl"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1 w-full">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-xl mb-2">
                                        @if($item->design)
                                            {{ is_array($item->design->name) ? ($item->design->name[app()->getLocale()] ?? $item->design->name[app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? __('designs.create_title')) : $item->design->name }}
                                        @else
                                            {{ __('designs.create_title') }}
                                        @endif
                                    </h3>
                                    @if($item->design && is_array($item->design->description))
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ is_array($item->design->description) ? ($item->design->description[app()->getLocale()] ?? $item->design->description[app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? '') : '' }}</p>
                                    @endif
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($item->subtotal, 2) }} ريال</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item->price, 2) }} × {{ $item->quantity }}</p>
                                </div>
                            </div>
                            
                            <!-- Selected Options -->
                            @php
                                $formattedOptions = $item->formatted_options ?? [];
                            @endphp
                            @if(!empty($formattedOptions))
                            <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-r-4 border-indigo-500 shadow-sm">
                                <p class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-cog text-indigo-600"></i>
                                    {{ __('orders.selected_options') }}:
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @if(isset($formattedOptions['size']))
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold shadow-sm border border-gray-200">
                                            <i class="fas fa-ruler text-indigo-600"></i>
                                            <span class="text-gray-700">{{ __('orders.size') }}:</span>
                                            <span class="text-indigo-600 font-bold">{{ is_array($formattedOptions['size']) ? ($formattedOptions['size']['name'] ?? $formattedOptions['size']['code'] ?? '') : $formattedOptions['size'] }}</span>
                                        </span>
                                    @endif
                                    
                                    @if(isset($formattedOptions['color']) && is_array($formattedOptions['color']))
                                        @foreach($formattedOptions['color'] as $color)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold shadow-sm border border-gray-200">
                                                <i class="fas fa-palette text-indigo-600"></i>
                                                <span class="text-gray-700">{{ __('orders.color') }}:</span>
                                                <span class="text-indigo-600 font-bold">{{ is_array($color['name']) ? ($color['name'][app()->getLocale()] ?? $color['name'][app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? '') : $color['name'] }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                    
                                    @if(isset($formattedOptions['fabric_type']) && is_array($formattedOptions['fabric_type']))
                                        @foreach($formattedOptions['fabric_type'] as $fabric)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold shadow-sm border border-gray-200">
                                                <i class="fas fa-tshirt text-indigo-600"></i>
                                                <span class="text-gray-700">{{ __('orders.fabric_type') }}:</span>
                                                <span class="text-indigo-600 font-bold">{{ is_array($fabric['name']) ? ($fabric['name'][app()->getLocale()] ?? $fabric['name'][app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? '') : $fabric['name'] }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                    
                                    @if(isset($formattedOptions['dome_type']) && is_array($formattedOptions['dome_type']))
                                        @foreach($formattedOptions['dome_type'] as $dome)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold shadow-sm border border-gray-200">
                                                <i class="fas fa-shapes text-indigo-600"></i>
                                                <span class="text-gray-700">{{ __('orders.dome_type') }}:</span>
                                                <span class="text-indigo-600 font-bold">{{ is_array($dome['name']) ? ($dome['name'][app()->getLocale()] ?? $dome['name'][app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? '') : $dome['name'] }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                    
                                    @if(isset($formattedOptions['sleeve_type']) && is_array($formattedOptions['sleeve_type']))
                                        @foreach($formattedOptions['sleeve_type'] as $sleeve)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold shadow-sm border border-gray-200">
                                                <i class="fas fa-hand-paper text-indigo-600"></i>
                                                <span class="text-gray-700">{{ __('orders.sleeve_type') }}:</span>
                                                <span class="text-indigo-600 font-bold">{{ is_array($sleeve['name']) ? ($sleeve['name'][app()->getLocale()] ?? $sleeve['name'][app()->getLocale() === 'ar' ? 'en' : 'ar'] ?? '') : $sleeve['name'] }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                                    <i class="fas fa-dollar-sign text-indigo-500"></i>
                                    <span class="font-medium">{{ __('orders.price') }}: <span class="text-gray-900">{{ number_format($item->price, 2) }} {{ __('common.currency') }}</span></span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                                    <i class="fas fa-shopping-cart text-indigo-500"></i>
                                    <span class="font-medium">{{ __('orders.quantity') }}: <span class="text-gray-900">{{ $item->quantity }}</span></span>
                                </div>
                                @if($item->size)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                                    <i class="fas fa-ruler text-indigo-500"></i>
                                    <span class="font-medium">{{ __('orders.size') }}: <span class="text-gray-900">{{ $item->size->name }} ({{ $item->size->code }})</span></span>
                                </div>
                                @endif
                            </div>
                            
                            @if($order->notes)
                            <div class="mt-4 p-4 bg-yellow-50 border-r-4 border-yellow-400 rounded-lg">
                                <p class="text-sm text-gray-700 flex items-start gap-2">
                                    <i class="fas fa-sticky-note text-yellow-600 mt-0.5"></i>
                                    <span><strong class="text-gray-900">{{ __('orders.notes') }}:</strong> {{ $order->notes }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-indigo-600"></i>
                </div>
                {{ __('orders.delivery_address') }}
            </h2>
            @if($order->location)
            <div class="bg-gradient-to-br from-gray-50 to-indigo-50 rounded-xl p-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('orders.city') }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->location->city ?? __('orders.not_available') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('orders.area') }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->location->area ?? __('orders.not_available') }}</p>
                    </div>
                    <div class="md:col-span-2 space-y-1 pt-4 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('orders.detailed_address') }}</p>
                        <p class="text-base font-semibold text-gray-900 leading-relaxed">{{ $order->location->detailed_address ?? __('orders.not_available') }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center text-gray-400 py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-map-marker-alt text-5xl mb-3"></i>
                <p class="text-lg font-medium">{{ __('orders.no_delivery_address') }}</p>
            </div>
            @endif
        </div>

        <!-- Order Notes -->
        @if($order->notes)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comment text-yellow-600"></i>
                </div>
                {{ __('orders.order_notes') }}
            </h2>
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-r-4 border-yellow-400 p-5 rounded-xl shadow-sm">
                <p class="text-gray-800 leading-relaxed">{{ $order->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Order Timeline -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-indigo-600"></i>
                </div>
                {{ __('orders.order_timeline') }}
            </h2>
            <div class="relative border-r-4 border-indigo-200 pr-8 space-y-8">
                <div class="relative">
                    <div class="absolute -right-[2.6rem] w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white shadow-lg ring-4 ring-green-100">
                        <i class="fas fa-check text-lg"></i>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-xl border-r-4 border-green-500">
                        <p class="font-bold text-gray-900 text-lg mb-1">{{ __('orders.order_created') }}</p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            {{ $order->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
                
                @if($order->confirmed_at)
                <div class="relative">
                    <div class="absolute -right-[2.6rem] w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white shadow-lg ring-4 ring-blue-100">
                        <i class="fas fa-check-double text-lg"></i>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-r-4 border-blue-500">
                        <p class="font-bold text-gray-900 text-lg mb-1">{{ __('orders.order_confirmed') }}</p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            {{ $order->confirmed_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
                @endif

                @if($order->status->value === 'delivered')
                <div class="relative">
                    <div class="absolute -right-[2.6rem] w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white shadow-lg ring-4 ring-purple-100">
                        <i class="fas fa-star text-lg"></i>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-xl border-r-4 border-purple-500">
                        <p class="font-bold text-gray-900 text-lg mb-1">{{ __('orders.order_completed') }}</p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            {{ $order->updated_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
                @elseif($order->status->value === 'cancelled')
                <div class="relative">
                    <div class="absolute -right-[2.6rem] w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center text-white shadow-lg ring-4 ring-red-100">
                        <i class="fas fa-times text-lg"></i>
                    </div>
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 p-4 rounded-xl border-r-4 border-red-500">
                        <p class="font-bold text-gray-900 text-lg mb-1">{{ __('orders.order_cancelled') }}</p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            {{ $order->updated_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        
        <!-- Customer Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-indigo-600"></i>
                </div>
                {{ __('orders.customer_info') }}
            </h2>
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                @if($order->user->avatar)
                    <img src="{{ Storage::url($order->user->avatar) }}" alt="{{ $order->user->name }}" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 shadow-md">
                @else
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ substr($order->user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <p class="font-bold text-gray-900 text-lg">{{ $order->user->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ __('orders.customer_since') }} {{ $order->user->created_at->format('Y') }}</p>
                </div>
            </div>
            <div class="space-y-4 mb-6">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ __('orders.email') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-phone text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ __('orders.phone') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->user->phone ?? __('orders.not_available') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ __('orders.total_orders') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ __('orders.orders_count', ['count' => $order->user->orders()->count()]) }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $order->user) }}" class="block w-full text-center bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 rounded-xl transition-all duration-200 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <i class="fas fa-user-circle ml-2"></i>
                {{ __('orders.view_profile') }}
            </a>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-green-600"></i>
                </div>
                {{ __('orders.payment_summary') }}
            </h2>
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">{{ __('orders.subtotal') }}</span>
                    <span class="font-bold text-gray-900 text-lg">{{ number_format($order->subtotal ?? $order->total_amount, 2) }} {{ __('common.currency') }}</span>
                </div>
                
                @if($order->coupon_id)
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">{{ __('orders.coupon_discount') }}</span>
                    <span class="font-bold text-green-600 text-lg">-{{ number_format($order->discount_amount ?? 0, 2) }} {{ __('common.currency') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">{{ __('orders.shipping') }}</span>
                    <span class="font-bold text-gray-900 text-lg">{{ number_format($order->shipping ?? 0, 2) }} {{ __('common.currency') }}</span>
                </div>
                
                <div class="flex justify-between items-center pt-4 bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-xl border-2 border-indigo-200">
                    <span class="text-xl font-bold text-gray-900">{{ __('orders.total') }}</span>
                    <span class="text-3xl font-bold text-indigo-600">{{ number_format($order->total_amount, 2) }} {{ __('common.currency') }}</span>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-gradient-to-r from-gray-50 to-indigo-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">طريقة الدفع</span>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold shadow-sm
                        @if($order->payment_method === 'cash') bg-green-100 text-green-800 border border-green-300
                        @elseif($order->payment_method === 'wallet') bg-purple-100 text-purple-800 border border-purple-300
                        @else bg-blue-100 text-blue-800 border border-blue-300
                        @endif
                    ">
                        <i class="fas 
                            @if($order->payment_method === 'cash') fa-money-bill-wave
                            @elseif($order->payment_method === 'wallet') fa-wallet
                            @else fa-credit-card
                            @endif
                        "></i>
                        {{ __('orders.payment_methods.' . $order->payment_method) }}
                    </span>
                </div>
            </div>
            
             @if($order->status === \App\Enums\OrderStatus::PAID)
             <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-300">
                 <div class="flex items-center gap-3 text-green-700">
                     <i class="fas fa-check-circle text-2xl"></i>
                     <div>
                         <p class="font-bold">{{ __('orders.payment_successful') }}</p>
                         <p class="text-xs text-green-600">{{ __('orders.amount_received') }}</p>
                     </div>
                 </div>
             </div>
             @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function printInvoice() {
    @if($order->invoice)
        // فتح صفحة الفاتورة في نافذة جديدة وطباعتها
        const invoiceUrl = '{{ route("dashboard.orders.invoice.view", $order) }}';
        const printWindow = window.open(invoiceUrl, '_blank');
        
        // بعد تحميل الصفحة، قم بالطباعة
        if (printWindow) {
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 500);
            };
        }
    @else
        alert('الفاتورة غير متاحة بعد. يرجى الانتظار حتى يتم إنشاء الفاتورة.');
    @endif
}

function showStatusModal() {
    // الحصول على الحالة الحالية
    const currentStatus = '{{ $order->status->value }}';
    
    // تحديد الحالة التالية المسموح بها
    let nextStatus = null;
    let statusOptions = [];
    
    switch(currentStatus) {
        case 'pending':
            nextStatus = 'paid';
            statusOptions = ['paid', 'cancelled'];
            break;
        case 'paid':
            nextStatus = 'confirmed';
            statusOptions = ['confirmed', 'cancelled'];
            break;
        case 'confirmed':
            nextStatus = 'processing';
            statusOptions = ['processing', 'cancelled'];
            break;
        case 'processing':
            nextStatus = 'shipped';
            statusOptions = ['shipped'];
            break;
        case 'shipped':
            nextStatus = 'delivered';
            statusOptions = ['delivered'];
            break;
        default:
            alert('لا يمكن تغيير حالة هذا الطلب');
            return;
    }
    
    // عرض قائمة بالحالات المسموح بها
    const statusLabels = {
        'paid': 'مدفوع',
        'confirmed': 'مؤكد',
        'processing': 'قيد التجهيز',
        'shipped': 'تم الشحن',
        'delivered': 'تم التوصيل',
        'cancelled': 'ملغي'
    };
    
    let message = 'الحالة الحالية: {{ $order->status->label() }}\n\n';
    message += 'الحالات المتاحة:\n';
    statusOptions.forEach((status, index) => {
        message += `${index + 1}. ${statusLabels[status]}\n`;
    });
    message += '\nأدخل رقم الحالة المطلوبة:';
    
    const choice = prompt(message);
    if (choice && statusOptions[parseInt(choice) - 1]) {
        updateStatus(statusOptions[parseInt(choice) - 1]);
    }
}

function updateStatus(status) {
    fetch(`/dashboard/orders/{{ $order->id }}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء تحديث الحالة');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء تحديث الحالة');
        console.error('Error:', error);
    });
}

function cancelOrder() {
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) {
        return;
    }

    fetch(`/admin/orders/{{ $order->id }}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إلغاء الطلب');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء إلغاء الطلب');
        console.error('Error:', error);
    });
}

function sendNotification() {
    const message = prompt('أدخل رسالة الإشعار:');
    if (message) {
        // Implement send notification
        alert('سيتم إرسال الإشعار إلى العميل');
    }
}
</script>
@endpush

