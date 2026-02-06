@extends('layouts.user')

@section('title', __('designs.browse_title'))

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('designs.browse_title') }}</h1>
    <p class="text-gray-600 mt-1">{{ __('designs.browse_subtitle') }}</p>
</div>

<!-- Filters & Search -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('designs.browse') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.search') }}</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="{{ __('designs.search_placeholder') }}"
                    class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Size Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.size') }}</label>
            <select name="size_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('designs.all_sizes') }}</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>
                        {{ $size->code }} - {{ $size->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Price Range -->
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.from') }}</label>
                <input 
                    type="number" 
                    name="min_price" 
                    value="{{ request('min_price') }}"
                    placeholder="0"
                    step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.to') }}</label>
                <input 
                    type="number" 
                    name="max_price" 
                    value="{{ request('max_price') }}"
                    placeholder="9999"
                    step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
        </div>

        <!-- Design Option Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.design_option') }}</label>
            <select name="design_option_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('designs.all_options') }}</option>
                @foreach($designOptions as $type => $group)
                    @if($group['options']->count() > 0)
                        <optgroup label="{{ isset(__('designs.option_types')[$type]) ? __('designs.option_types.' . $type) : $group['label_ar'] }}">
                            @foreach($group['options'] as $option)
                                <option value="{{ $option->id }}" {{ request('design_option_id') == $option->id ? 'selected' : '' }}>
                                    {{ $option->getTranslation('name', app()->getLocale(), true) ?: $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Creator Filter -->
        @if($creators->count() > 0)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('designs.designer') }}</label>
            <select name="creator_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('designs.all_designers') }}</option>
                @foreach($creators as $creator)
                    <option value="{{ $creator->id }}" {{ request('creator_id') == $creator->id ? 'selected' : '' }}>
                        {{ $creator->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter ml-2"></i>
                {{ __('designs.apply_filters') }}
            </button>
            <a href="{{ route('designs.browse') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo ml-2"></i>
                {{ __('common.reset') }}
            </a>
        </div>
    </form>
</div>

<!-- Designs Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($designs as $design)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
        <!-- Design Image (صورة التصميم أو الصورة الافتراضية) -->
        <div class="relative aspect-square bg-gray-100">
            <img
                src="{{ $design->display_image_url }}"
                alt="{{ $design->getTranslation('name', app()->getLocale(), true) ?: $design->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                onerror="this.src='{{ asset(\App\Models\Design::PLACEHOLDER_IMAGE_PATH) }}'"
            >
            
            <!-- View Button Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                <a href="#" class="bg-white text-gray-800 p-3 rounded-full hover:bg-gray-100 transition">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>

        <!-- Design Info -->
        <div class="p-4">
            <h3 class="font-bold text-gray-900 text-lg mb-2 truncate">{{ $design->getTranslation('name', app()->getLocale(), true) ?: $design->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $design->getTranslation('description', app()->getLocale(), true) ?: $design->getTranslation('description', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}</p>
            
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl font-bold text-indigo-600">{{ number_format($design->price, 2) }} {{ __('common.sar') }}</span>
                <span class="text-sm text-gray-500">{{ $design->sizes->count() }} {{ __('designs.size_count') }}</span>
            </div>

            <!-- Creator Info -->
            <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
                @if($design->user->avatar)
                    <img src="{{ Storage::url($design->user->avatar) }}" alt="{{ $design->user->name }}" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ substr($design->user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $design->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $design->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Sizes -->
            @if($design->sizes->count() > 0)
            <div class="flex flex-wrap gap-1 mt-3">
                @foreach($design->sizes as $size)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $size->code }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-palette text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('designs.no_designs') }}</h3>
            <p class="text-gray-600">{{ __('designs.no_designs_desc') }}</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($designs->hasPages())
<div class="mt-6">
    {{ $designs->links() }}
</div>
@endif

@endsection

