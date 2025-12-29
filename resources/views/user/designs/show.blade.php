@extends('layouts.user')

@section('title', $design->getTranslation('name', 'ar'))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $design->getTranslation('name', 'ar') }}</h1>
        <p class="text-gray-600 mt-1">تفاصيل التصميم</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('my-designs.edit', $design) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-edit"></i>
            <span>تعديل</span>
        </a>
        <form action="{{ route('my-designs.destroy', $design) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصميم؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-trash"></i>
                <span>حذف</span>
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
            <h2 class="text-xl font-bold text-gray-800 mb-4">الصور</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($design->images as $image)
                    <div class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img 
                            src="{{ Storage::url($image->image_path) }}" 
                            alt="Design Image"
                            class="w-full h-full object-cover"
                        >
                        @if($image->is_primary)
                            <div class="absolute top-2 right-2 bg-indigo-600 text-white px-2 py-1 rounded text-xs font-bold">
                                رئيسية
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Description -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">الوصف</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">العربية</h3>
                    <p class="text-gray-800">{{ $design->getTranslation('description', 'ar') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">English</h3>
                    <p class="text-gray-800">{{ $design->getTranslation('description', 'en') }}</p>
                </div>
            </div>
        </div>

        <!-- Sizes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">المقاسات المتاحة</h2>
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
            <h2 class="text-xl font-bold text-gray-800 mb-4">خيارات التصميم</h2>
            <div class="space-y-4">
                @php
                    $groupedOptions = $design->designOptions->groupBy('type');
                @endphp
                @foreach($groupedOptions as $type => $options)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">
                            @if($type === 'color')
                                اللون
                            @elseif($type === 'dome_type')
                                نوع القبة
                            @elseif($type === 'fabric_type')
                                نوع القماش
                            @elseif($type === 'sleeve_type')
                                نوع الأكمام
                            @else
                                {{ $type }}
                            @endif
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($options as $option)
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                                    {{ $option->getTranslation('name', 'ar') }}
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
                <h3 class="text-sm font-semibold text-gray-600 mb-2">السعر</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($design->price, 2) }} ر.س</p>
            </div>

            <!-- Status -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">الحالة</h3>
                @if($design->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle ml-1"></i>
                        نشط
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-pause-circle ml-1"></i>
                        غير نشط
                    </span>
                @endif
            </div>

            <!-- Dates -->
            <div class="space-y-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">تاريخ الإنشاء</h3>
                    <p class="text-sm text-gray-800">{{ $design->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">آخر تحديث</h3>
                    <p class="text-sm text-gray-800">{{ $design->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t space-y-2">
                <a href="{{ route('my-designs.edit', $design) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-center transition">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل التصميم
                </a>
                <a href="{{ route('my-designs.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium text-center transition">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

