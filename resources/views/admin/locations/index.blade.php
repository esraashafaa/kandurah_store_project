@extends('layouts.admin')

@section('title', 'إدارة المواقع')

@push('styles')
<style>
    .search-btn {
        background: linear-gradient(to right, #4f46e5, #7c3aed) !important;
        border: none !important;
    }
    .search-btn:hover {
        background: linear-gradient(to right, #4338ca, #6d28d9) !important;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">إدارة المواقع</h1>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع مواقع المستخدمين</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="w-full flex gap-4 mb-8 flex-nowrap items-stretch">
    
    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-blue-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-map-marker-alt text-blue-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">إجمالي المواقع</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total_locations'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-green-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-city text-green-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">عدد المدن</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total_cities'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-purple-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-purple-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">المستخدمين بالمواقع</p>
            <p class="text-4xl font-bold text-gray-900">{{ $stats['total_users_with_locations'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-r-4 border-orange-500 hover:shadow-lg transition-shadow
                flex flex-col justify-between items-center text-center w-1/4 min-h-[200px]">
        <div class="w-16 h-16 min-w-[4rem] min-h-[4rem] bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-map text-orange-600 text-3xl" style="display: inline-block !important; font-family: 'Font Awesome 6 Free' !important; font-weight: 900 !important;"></i>
        </div>
        <div class="flex-shrink-0">
            <p class="text-gray-600 text-sm font-medium mb-2">المواقع الافتراضية</p>
            <p class="text-4xl font-bold text-gray-900">{{ \App\Models\Location::where('is_default', true)->count() }}</p>
        </div>
    </div>

</div>

<!-- Filters & Search -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="GET" action="{{ route('dashboard.locations.index') }}" class="space-y-4">
        <div class="flex flex-col lg:flex-row gap-4">
            
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="ابحث في المدينة، المنطقة، الشارع، أو اسم المستخدم..."
                        class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                    >
                    <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
                </div>
            </div>

            <!-- City Filter -->
            <div class="w-full lg:w-48">
                <select name="city" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="">جميع المدن</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Area Filter -->
            <div class="w-full lg:w-48">
                <input 
                    type="text" 
                    name="area" 
                    value="{{ request('area') }}"
                    placeholder="المنطقة..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                >
            </div>

            <!-- User Filter -->
            <div class="w-full lg:w-48">
                <select name="user_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="">جميع المستخدمين</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Default Location Filter -->
            <div class="w-full lg:w-48">
                <select name="is_default" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="">جميع المواقع</option>
                    <option value="1" {{ request('is_default') === '1' ? 'selected' : '' }}>افتراضية فقط</option>
                    <option value="0" {{ request('is_default') === '0' ? 'selected' : '' }}>غير افتراضية</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="w-full lg:w-48">
                <select name="sort_by" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                    <option value="city" {{ request('sort_by') === 'city' ? 'selected' : '' }}>المدينة</option>
                    <option value="area" {{ request('sort_by') === 'area' ? 'selected' : '' }}>المنطقة</option>
                </select>
            </div>

            <!-- Sort Direction -->
            <div class="w-full lg:w-48">
                <select name="sort_direction" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>تنازلي</option>
                    <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                </select>
            </div>

            <!-- Per Page -->
            <div class="w-full lg:w-48">
                <select name="per_page" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 لكل صفحة</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 لكل صفحة</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 لكل صفحة</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 لكل صفحة</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="search-btn text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-filter"></i>
                    <span class="hidden sm:inline">بحث</span>
                </button>
                <a href="{{ route('dashboard.locations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 inline-flex items-center gap-2">
                    <i class="fas fa-redo"></i>
                    <span class="hidden sm:inline">إعادة تعيين</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Locations Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المدينة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المنطقة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الشارع</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإحداثيات</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">افتراضي</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($locations as $location)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $location->city }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $location->area }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $location->street }}</div>
                        <div class="text-xs text-gray-500">{{ $location->house_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <div>Lat: {{ number_format($location->lat, 6) }}</div>
                            <div>Lng: {{ number_format($location->lng, 6) }}</div>
                        </div>
                        <a href="{{ $location->google_maps_url }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-flex items-center gap-1">
                            <i class="fas fa-external-link-alt"></i>
                            عرض على الخريطة
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($location->user)
                            <div class="text-sm font-medium text-gray-900">{{ $location->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $location->user->email }}</div>
                        @else
                            <span class="text-sm text-gray-400">غير متوفر</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($location->is_default)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle ml-1"></i>
                                افتراضي
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                -
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $location->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs text-gray-500">{{ $location->created_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-map-marker-alt text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-semibold mb-2 text-gray-700">لا توجد مواقع</p>
                            <p class="text-sm text-gray-500">لم يتم العثور على مواقع مطابقة للبحث</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($locations->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $locations->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Ensure Font Awesome icons are always visible
(function() {
    const fixIcons = () => {
        document.querySelectorAll('i.fas, i.far, i.fal, i.fab').forEach(icon => {
            icon.style.setProperty('font-family', 'Font Awesome 6 Free', 'important');
            icon.style.setProperty('font-weight', '900', 'important');
            icon.style.setProperty('display', 'inline-block', 'important');
            icon.style.setProperty('visibility', 'visible', 'important');
            icon.style.setProperty('opacity', '1', 'important');
            icon.style.setProperty('font-style', 'normal', 'important');
        });
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixIcons);
    } else {
        fixIcons();
    }
    
    [100, 300, 500, 1000, 2000].forEach(delay => {
        setTimeout(fixIcons, delay);
    });
})();
</script>
@endpush

