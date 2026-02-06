@extends('layouts.admin')

@section('title', __('designs.details'))

@section('content')

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">
                <i class="fas fa-home ml-2"></i>
                {{ __('sidebar.home') }}
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <a href="{{ route('dashboard.designs.index') }}" class="text-gray-600 hover:text-indigo-600">{{ __('sidebar.designs') }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ $design->name ?? __('designs.design_number') . $design->id }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Design Header -->
<div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-lg p-6 mb-6 text-white">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold mb-2">{{ $design->name ?? __('designs.design_number') . $design->id }}</h1>
            <p class="text-purple-100">{{ __('designs.created_at_text') }} {{ $design->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex gap-2">
            @if($design->order_items_count > 0)
            <span class="bg-white bg-opacity-20 px-4 py-2 rounded-lg">
                <i class="fas fa-shopping-cart ml-2"></i>
                {{ __('designs.orders_count', ['count' => $design->order_items_count]) }}
            </span>
            @endif
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column - Images -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Design Images -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.design_images') }}</h2>
            
            @if($design->images && $design->images->count() > 0)
            <div class="grid grid-cols-2 gap-4">
                @foreach($design->images as $image)
                <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img 
                        src="{{ $image->image_url }}" 
                        alt="{{ __('designs.image_alt') }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'30\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <a href="{{ $image->image_url }}" target="_blank" class="bg-white text-gray-800 p-3 rounded-full hover:bg-gray-100 transition">
                            <i class="fas fa-expand"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">{{ __('designs.no_images') }}</p>
            </div>
            @endif
        </div>

        <!-- Design Options -->
        @if($design->designOptions && $design->designOptions->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.design_options') }}</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($design->designOptions as $option)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-500 transition">
                    @if($option->image_path)
                    <img src="{{ Storage::disk('public')->url($option->image_path) }}" alt="{{ $option->name }}" class="w-full h-24 object-cover rounded-lg mb-2" onerror="this.style.display='none'">
                    @endif
                    <p class="text-sm font-semibold text-gray-900">{{ $option->name }}</p>
                    <p class="text-xs text-gray-500">{{ $option->type ?? __('designs.option') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Design Sizes -->
        @if($design->sizes && $design->sizes->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.sizes') }}</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($design->sizes as $size)
                <div class="border border-gray-200 rounded-lg p-3 text-center hover:border-indigo-500 transition">
                    <p class="font-bold text-gray-900">{{ $size->name }}</p>
                    <p class="text-xs text-gray-500">{{ $size->value ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($design->notes)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-sticky-note text-yellow-500"></i>
                {{ __('designs.design_notes') }}
            </h2>
            <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4 rounded">
                <p class="text-gray-700">{{ $design->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    <!-- Right Column - Info -->
    <div class="space-y-6">
        
        <!-- Design Details -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.design_details') }}</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">{{ __('designs.design_id') }}</p>
                    <p class="font-semibold text-gray-900">#{{ $design->id }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">{{ __('designs.fabric_type_label') }}</p>
                    <p class="font-semibold text-gray-900">{{ $design->fabric_type ?? __('common.not_set') }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">{{ __('designs.color_label') }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($design->color)
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: {{ $design->color }}"></div>
                            <span class="font-semibold text-gray-900">{{ $design->color }}</span>
                        @else
                            <span class="text-gray-400">{{ __('common.not_set') }}</span>
                        @endif
                    </div>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">{{ __('designs.quantity_label') }}</p>
                    <p class="font-semibold text-gray-900">{{ $design->quantity ?? 1 }}</p>
                </div>
                <hr>
                <div>
                    <p class="text-sm text-gray-600">{{ __('designs.created_at_label') }}</p>
                    <p class="font-semibold text-gray-900">{{ $design->created_at->format('Y-m-d') }}</p>
                    <p class="text-xs text-gray-500">{{ $design->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.customer_info') }}</h2>
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200">
                @if($design->user->avatar)
                    <img src="{{ Storage::disk('public')->url($design->user->avatar) }}" alt="{{ $design->user->name }}" class="w-16 h-16 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                @else
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ substr($design->user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <p class="font-bold text-gray-900">{{ $design->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $design->user->email }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-phone text-gray-400 w-5"></i>
                    <span class="text-gray-700">{{ $design->user->phone ?? __('common.not_set') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-palette text-gray-400 w-5"></i>
                    <span class="text-gray-700">{{ __('designs.designs_count', ['count' => $design->user->designs()->count()]) }}</span>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $design->user) }}" class="mt-4 block text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-2 rounded-lg transition">
                {{ __('designs.view_profile') }}
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('designs.quick_actions') }}</h2>
            <div class="space-y-2">
                <button onclick="sendNotification()" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas fa-bell text-indigo-600"></i>
                    <span class="text-gray-700">{{ __('designs.send_notification') }}</span>
                </button>
                <button onclick="printDesign()" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas fa-print text-green-600"></i>
                    <span class="text-gray-700">{{ __('designs.print_design') }}</span>
                </button>
                <button onclick="deleteDesign({{ $design->id }})" class="w-full text-right px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                    <i class="fas fa-trash text-red-600"></i>
                    <span class="text-gray-700">{{ __('designs.delete_design') }}</span>
                </button>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function sendNotification() {
    alert('{{ __('designs.send_notification') }}');
}

function printDesign() {
    window.print();
}

function deleteDesign(designId) {
    if (!confirm('{{ __('designs.delete_confirm') }} {{ __('common.confirm_delete_item') }}')) {
        return;
    }

    fetch(`/dashboard/designs/${designId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("dashboard.designs.index") }}';
        } else {
            alert(data.message || '{{ __('common.error') }}');
        }
    })
    .catch(error => {
        alert('{{ __('common.error') }}');
        console.error('Error:', error);
    });
}
</script>
@endpush

