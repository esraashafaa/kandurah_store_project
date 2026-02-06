@extends('layouts.admin')

@section('title', __('reviews.title'))

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('reviews.title') }}</h1>
    <p class="text-gray-600 mt-1">{{ __('reviews.subtitle') }}</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-gray-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('reviews.stats.total') }}</p>
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
                <p class="text-sm text-gray-600">{{ __('reviews.stats.approved') }}</p>
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
                <p class="text-sm text-gray-600">{{ __('reviews.stats.pending') }}</p>
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
                <p class="text-sm text-gray-600">{{ __('reviews.stats.rejected') }}</p>
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
                <p class="text-sm text-gray-600">{{ __('reviews.stats.average') }}</p>
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
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('reviews.filters.search') }}</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="{{ __('reviews.filters.search_placeholder') }}"
                    class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('reviews.filters.status') }}</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('reviews.filters.all_statuses') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('reviews.status.pending') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('reviews.status.approved') }}</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('reviews.status.rejected') }}</option>
            </select>
        </div>

        <!-- Rating Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('reviews.filters.rating') }}</label>
            <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('reviews.filters.all_ratings') }}</option>
                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>{{ __('reviews.rating.5_stars') }}</option>
                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>{{ __('reviews.rating.4_stars') }}</option>
                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>{{ __('reviews.rating.3_stars') }}</option>
                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>{{ __('reviews.rating.2_stars') }}</option>
                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>{{ __('reviews.rating.1_star') }}</option>
            </select>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter ml-2"></i>
                {{ __('reviews.filters.apply') }}
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo ml-2"></i>
                {{ __('reviews.filters.reset') }}
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
                        <span>{{ __('reviews.order.order') }} #{{ $review->order->id }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar text-gray-400"></i>
                        <span>{{ $review->order->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
                @endif

                <!-- Status & Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle"></i>
                            {{ __('reviews.status.active') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard.orders.show', $review->order) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-shopping-cart ml-1"></i>
                            {{ __('reviews.actions.view_order') }}
                        </a>
                        
                        <a href="{{ route('admin.users.show', $review->user) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-user ml-1"></i>
                            {{ __('reviews.actions.view_profile') }}
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
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('reviews.empty.title') }}</h3>
        <p class="text-gray-600">{{ __('reviews.empty.description') }}</p>
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
function deleteReview(reviewId) {
    if (!confirm('{{ __('reviews.messages.delete_confirm') }}')) {
        return;
    }

    fetch(`/api/reviews/${reviewId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            window.location.reload();
        } else {
            alert(data.message || '{{ __('reviews.messages.delete_error') }}');
        }
    })
    .catch(error => {
        alert('{{ __('reviews.messages.delete_error') }}');
        console.error('Error:', error);
    });
}
</script>
@endpush

