@extends('layouts.user')

@section('title', $design->getTranslation('name', app()->getLocale(), true) ?: $design->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $design->getTranslation('name', app()->getLocale(), true) ?: $design->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}</h1>
        <p class="text-gray-600 mt-1">{{ __('designs.details') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('my-designs.edit', $design) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-edit"></i>
            <span>{{ __('common.edit') }}</span>
        </a>
        <form action="{{ route('my-designs.destroy', $design) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('designs.confirm_delete') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-trash"></i>
                <span>{{ __('common.delete') }}</span>
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Images Gallery -->
        @if($design->images->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.images') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($design->images as $image)
                    <div class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img 
                            src="{{ $image->image_url }}" 
                            alt="Design Image"
                            class="w-full h-full object-cover"
                        >
                        @if($image->is_primary)
                            <div class="absolute top-2 right-2 bg-indigo-600 text-white px-2 py-1 rounded text-xs font-bold">
                                {{ __('designs.primary') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Description -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.description') }}</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">{{ __('designs.arabic') }}</h3>
                    <p class="text-gray-800">{{ $design->getTranslation('description', app()->getLocale(), true) ?: $design->getTranslation('description', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">{{ __('designs.english') }}</h3>
                    <p class="text-gray-800">{{ $design->getTranslation('description', 'en') }}</p>
                </div>
            </div>
        </div>

        <!-- Sizes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.available_sizes') }}</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($design->sizes as $size)
                    <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg font-medium">
                        {{ $size->code }} - {{ $size->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Design Options -->
        @if($design->designOptions->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.design_options') }}</h2>
            <div class="space-y-4">
                @php
                    $groupedOptions = $design->designOptions->groupBy('type');
                @endphp
                @foreach($groupedOptions as $type => $options)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">
                            @if(isset(__('designs.option_types')[$type]))
                                {{ __('designs.option_types.' . $type) }}
                            @else
                                {{ $type }}
                            @endif
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($options as $option)
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                                    {{ $option->getTranslation('name', app()->getLocale(), true) ?: $option->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar', true) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6 sticky top-4">
            
            <!-- Price -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">{{ __('designs.price') }}</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($design->price, 2) }} {{ __('common.sar') }}</p>
            </div>

            <!-- Status -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">{{ __('designs.status') }}</h3>
                @if($design->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle ml-1"></i>
                        {{ __('common.active') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-pause-circle ml-1"></i>
                        {{ __('common.inactive') }}
                    </span>
                @endif
            </div>

            <!-- Dates -->
            <div class="space-y-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">{{ __('designs.created_at') }}</h3>
                    <p class="text-sm text-gray-800">{{ $design->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">{{ __('designs.updated_at') }}</h3>
                    <p class="text-sm text-gray-800">{{ $design->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t space-y-2">
                <a href="{{ route('my-designs.edit', $design) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-center transition">
                    <i class="fas fa-edit ml-2"></i>
                    {{ __('designs.edit_design') }}
                </a>
                <a href="{{ route('my-designs.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium text-center transition">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('designs.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

