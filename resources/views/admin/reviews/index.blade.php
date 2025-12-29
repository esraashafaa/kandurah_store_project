@extends('layouts.admin')

@section('title', 'إدارة التقييمات')

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">إدارة التقييمات</h1>
    <p class="text-gray-600 mt-1">عرض ومراجعة تقييمات العملاء</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-gray-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي التقييمات</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-star text-gray-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">المعتمدة</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['approved'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد المراجعة</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">المرفوضة</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">متوسط التقييم</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['average'] ?? 0, 1) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ابحث باسم العميل أو التعليق..."
                    class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>
        </div>

        <!-- Rating Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">التقييم</label>
            <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">جميع التقييمات</option>
                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 نجوم</option>
                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 نجوم</option>
                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>⭐⭐⭐ 3 نجوم</option>
                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>⭐⭐ 2 نجمتان</option>
                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>⭐ 1 نجمة</option>
            </select>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo ml-2"></i>
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Reviews List -->
<div class="space-y-4">
    @forelse($reviews as $review)
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-start gap-4">
            <!-- User Avatar -->
            @if($review->user->profile_image)
                <img src="{{ Storage::url($review->user->profile_image) }}" alt="{{ $review->user->name }}" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
            @else
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-500 to-pink-600 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                    {{ substr($review->user->name, 0, 1) }}
                </div>
            @endif

            <!-- Review Content -->
            <div class="flex-1">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $review->user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Rating Stars -->
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-gray-700">{{ $review->rating }}</span>
                    </div>
                </div>

                <!-- Review Text -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-gray-700">{{ $review->comment }}</p>
                </div>

                <!-- Order Info -->
                @if($review->order)
                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-gray-400"></i>
                        <span>طلب #{{ $review->order->id }}</span>
                    </div>
                    @if($review->design)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-palette text-gray-400"></i>
                        <span>{{ $review->design->name }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Status & Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                            @if($review->status === 'approved') bg-green-100 text-green-800
                            @elseif($review->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif
                        ">
                            <i class="fas 
                                @if($review->status === 'approved') fa-check-circle
                                @elseif($review->status === 'pending') fa-clock
                                @else fa-times-circle
                                @endif
                            "></i>
                            {{ __('reviews.status.' . $review->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($review->status === 'pending')
                        <button onclick="approveReview({{ $review->id }})" class="bg-green-50 hover:bg-green-100 text-green-600 px-4 py-2 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-check ml-1"></i>
                            اعتماد
                        </button>
                        <button onclick="rejectReview({{ $review->id }})" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-times ml-1"></i>
                            رفض
                        </button>
                        @endif
                        
                        <a href="{{ route('admin.users.show', $review->user) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-user ml-1"></i>
                            الملف الشخصي
                        </a>
                        
                        <button onclick="deleteReview({{ $review->id }})" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-star text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد تقييمات</h3>
        <p class="text-gray-600">لم يتم العثور على أي تقييمات تطابق معايير البحث</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($reviews->hasPages())
<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
function approveReview(reviewId) {
    updateReviewStatus(reviewId, 'approved');
}

function rejectReview(reviewId) {
    updateReviewStatus(reviewId, 'rejected');
}

function updateReviewStatus(reviewId, status) {
    const confirmMessage = status === 'approved' ? 'هل أنت متأكد من اعتماد هذا التقييم؟' : 'هل أنت متأكد من رفض هذا التقييم؟';
    
    if (!confirm(confirmMessage)) {
        return;
    }

    fetch(`/admin/reviews/${reviewId}/status`, {
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
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        alert('حدث خطأ');
        console.error('Error:', error);
    });
}

function deleteReview(reviewId) {
    if (!confirm('هل أنت متأكد من حذف هذا التقييم؟ لا يمكن التراجع عن هذا الإجراء.')) {
        return;
    }

    fetch(`/admin/reviews/${reviewId}`, {
        method: 'DELETE',
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
            alert(data.message || 'حدث خطأ أثناء حذف التقييم');
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء حذف التقييم');
        console.error('Error:', error);
    });
}
</script>
@endpush

