@extends('layouts.user')

@section('title', 'تصاميمي')

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">تصاميمي</h1>
        <p class="text-gray-600 mt-1">إدارة وتنظيم جميع تصاميمك</p>
    </div>
    <a href="{{ route('my-designs.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>تصميم جديد</span>
    </a>
</div>

<!-- Filters & Search -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('my-designs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ابحث بالاسم أو الوصف..."
                    class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Size Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المقاس</label>
            <select name="size_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">جميع المقاسات</option>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">من</label>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى</label>
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

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <a href="{{ route('my-designs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo ml-2"></i>
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Designs Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($designs as $design)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
        <!-- Design Image -->
        <div class="relative aspect-square bg-gray-100">
            @if($design->images && $design->images->first())
                <img 
                    src="{{ Storage::url($design->images->first()->image_path) }}" 
                    alt="{{ $design->getTranslation('name', 'ar') }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                >
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-palette text-6xl text-gray-300"></i>
                </div>
            @endif
            
            <!-- Quick Actions Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                <a href="{{ route('my-designs.show', $design) }}" class="bg-white text-gray-800 p-3 rounded-full hover:bg-gray-100 transition">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('my-designs.edit', $design) }}" class="bg-blue-500 text-white p-3 rounded-full hover:bg-blue-600 transition">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('my-designs.destroy', $design) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصميم؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white p-3 rounded-full hover:bg-red-600 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            
            <!-- Status Badge -->
            @if($design->is_active)
            <div class="absolute top-2 right-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                <i class="fas fa-check-circle ml-1"></i>
                نشط
            </div>
            @else
            <div class="absolute top-2 right-2 bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                <i class="fas fa-pause-circle ml-1"></i>
                غير نشط
            </div>
            @endif
        </div>

        <!-- Design Info -->
        <div class="p-4">
            <h3 class="font-bold text-gray-900 text-lg mb-2 truncate">{{ $design->getTranslation('name', 'ar') }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $design->getTranslation('description', 'ar') }}</p>
            
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl font-bold text-indigo-600">{{ number_format($design->price, 2) }} ر.س</span>
                <span class="text-sm text-gray-500">{{ $design->sizes->count() }} مقاس</span>
            </div>

            <!-- Sizes -->
            @if($design->sizes->count() > 0)
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($design->sizes as $size)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $size->code }}</span>
                @endforeach
            </div>
            @endif

            <!-- Date -->
            <p class="text-xs text-gray-400">{{ $design->created_at->diffForHumans() }}</p>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-palette text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد تصاميم</h3>
            <p class="text-gray-600 mb-6">لم تقم بإنشاء أي تصاميم بعد</p>
            <a href="{{ route('my-designs.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                <i class="fas fa-plus ml-2"></i>
                إنشاء تصميم جديد
            </a>
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

