@extends('layouts.admin')

@section('title', $option->getTranslation('name', 'ar'))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $option->getTranslation('name', 'ar') }}</h1>
        <p class="text-gray-600 mt-1">تفاصيل خيار التصميم</p>
    </div>
    @canany(['update', 'delete'], $option)
        <div class="flex gap-2">
            @can('update', $option)
                <a href="{{ route('dashboard.design-options.edit', $option) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>
            @endcan
            @can('delete', $option)
                <form action="{{ route('dashboard.design-options.destroy', $option) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخيار؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        <span>حذف</span>
                    </button>
                </form>
            @endcan
        </div>
    @endcanany
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">المعلومات الأساسية</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">الاسم</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">العربية</p>
                            <p class="text-gray-800 font-medium">{{ $option->getTranslation('name', 'ar') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">English</p>
                            <p class="text-gray-800 font-medium">{{ $option->getTranslation('name', 'en') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">النوع</h3>
                    <span class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg font-medium">
                        <i class="fas fa-tag ml-2"></i>
                        {{ $option->type->labelAr() }} ({{ $option->type->label() }})
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">الحالة</h3>
                    @if($option->is_active)
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg font-medium">
                            <i class="fas fa-check-circle ml-2"></i>
                            نشط
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg font-medium">
                            <i class="fas fa-pause-circle ml-2"></i>
                            غير نشط
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Designs Using This Option -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">التصاميم التي تستخدم هذا الخيار</h2>
            @if($option->designs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($option->designs as $design)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start gap-3">
                                @if($design->images->first())
                                    <img 
                                        src="{{ Storage::url($design->images->first()->image_path) }}" 
                                        alt="{{ $design->getTranslation('name', 'ar') }}"
                                        class="w-16 h-16 object-cover rounded-lg"
                                    >
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-palette text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('dashboard.designs.show', $design) }}" class="hover:text-indigo-600">
                                            {{ $design->getTranslation('name', 'ar') }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ number_format($design->price, 2) }} ر.س</p>
                                    <p class="text-xs text-gray-500">بواسطة: {{ $design->user->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-palette text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-600">لا توجد تصاميم تستخدم هذا الخيار</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6 sticky top-4">
            
            <!-- Statistics -->
            <div>
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">الإحصائيات</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">عدد التصاميم</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $option->designs->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">الحالة</span>
                        @if($option->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">نشط</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">غير نشط</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="space-y-2 pt-4 border-t">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">تاريخ الإنشاء</h3>
                    <p class="text-sm text-gray-800">{{ $option->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">آخر تحديث</h3>
                    <p class="text-sm text-gray-800">{{ $option->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t space-y-2">
                @can('update', $option)
                    <a href="{{ route('dashboard.design-options.edit', $option) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-center transition">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الخيار
                    </a>
                @endcan
                <a href="{{ route('dashboard.design-options.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium text-center transition">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

