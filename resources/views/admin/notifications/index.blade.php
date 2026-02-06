@extends('layouts.admin')

@section('title', __('notifications.page_title'))

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('notifications.page_title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('notifications.subtitle') }}</p>
    </div>
    @if($isSuperAdmin ?? false)
    <div class="flex gap-3">
        <form action="{{ route('admin.notifications.test') }}" method="POST" class="inline" onsubmit="return confirm('{{ __('notifications.confirm_send_test') }}');">
            @csrf
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-vial"></i>
                <span>{{ __('notifications.send_test') }}</span>
            </button>
        </form>
        <button onclick="showSendModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-paper-plane"></i>
            <span>{{ __('notifications.send_new') }}</span>
        </button>
    </div>
    @endif
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('notifications.total') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bell text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('notifications.read') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['read'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope-open text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('notifications.unread') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['unread'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-r-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">{{ __('notifications.today') }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['today'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-day text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.search') }}</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="{{ __('notifications.search_placeholder') }}"
                    class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.status') }}</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('notifications.all_statuses') }}</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>{{ __('notifications.status_read') }}</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>{{ __('notifications.status_unread') }}</option>
            </select>
        </div>

        <!-- Type Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.type') }}</label>
            <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">{{ __('notifications.all_types') }}</option>
                <option value="order" {{ request('type') === 'order' ? 'selected' : '' }}>{{ __('notifications.type_order') }}</option>
                <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>{{ __('notifications.type_system') }}</option>
                <option value="design_created" {{ request('type') === 'design_created' ? 'selected' : '' }}>{{ __('notifications.type_design_created') }}</option>
                <option value="promotion" {{ request('type') === 'promotion' ? 'selected' : '' }}>{{ __('notifications.type_promotion') }}</option>
            </select>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter ml-2"></i>
                {{ __('notifications.apply_filters') }}
            </button>
            <a href="{{ route('admin.notifications.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo ml-2"></i>
                {{ __('common.reset') }}
            </a>
        </div>
    </form>
</div>

<!-- Notifications List -->
<div class="space-y-4">
    @forelse($notifications as $notification)
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition {{ $notification->read_at ? 'opacity-75' : '' }}">
        <div class="flex items-start gap-4">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full flex items-center justify-center
                    @if($notification->type === 'order') bg-blue-100
                    @elseif($notification->type === 'system') bg-purple-100
                    @elseif($notification->type === 'design_created') bg-indigo-100
                    @else bg-orange-100
                    @endif
                ">
                    <i class="fas text-xl
                        @if($notification->type === 'order') fa-shopping-cart text-blue-600
                        @elseif($notification->type === 'system') fa-cog text-purple-600
                        @elseif($notification->type === 'design_created') fa-palette text-indigo-600
                        @else fa-bullhorn text-orange-600
                        @endif
                    "></i>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $notification->title }}</h3>
                        @if(!$notification->read_at)
                        <span class="inline-block px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full mt-1">
                            <i class="fas fa-circle text-xs ml-1"></i>
                            {{ __('notifications.new_badge') }}
                        </span>
                        @endif
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-4">{{ $notification->message }}</p>

                <div class="flex items-center justify-between">
                    <div></div>
                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        @if(!$notification->read_at)
                        <button onclick="markAsRead('{{ $notification->id }}')" class="text-green-600 hover:text-green-900 text-sm">
                            <i class="fas fa-check ml-1"></i>
                            {{ __('common.mark_read') }}
                        </button>
                        @endif
                        <button onclick="deleteNotification('{{ $notification->id }}')" class="text-red-600 hover:text-red-900 text-sm">
                            <i class="fas fa-trash ml-1"></i>
                            {{ __('common.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('common.no_notifications') }}</h3>
        <p class="text-gray-600 mb-6">{{ __('notifications.no_notifications_subtitle') }}</p>
        @if($isSuperAdmin ?? false)
        <button onclick="showSendModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-paper-plane"></i>
            <span>{{ __('notifications.send_new') }}</span>
        </button>
        @endif
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($notifications->hasPages())
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif

<!-- Send Notification Modal (Simple Implementation) -->
<div id="sendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('notifications.modal_title') }}</h2>
            <button onclick="closeSendModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.notifications.send') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <!-- Recipient Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.recipient') }}</label>
                    <select name="recipient_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onchange="toggleUserSelect(this.value)">
                        <option value="all">{{ __('notifications.all_users') }}</option>
                        <option value="specific">{{ __('notifications.specific_user') }}</option>
                    </select>
                </div>

                <!-- Specific User (Hidden by default) -->
                <div id="userSelect" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.choose_user') }}</label>
                    <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">{{ __('notifications.choose_user_placeholder') }}</option>
                        @foreach($usersForSelect ?? [] as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    @if(empty($usersForSelect) || $usersForSelect->isEmpty())
                        <p class="text-amber-600 text-sm mt-1">{{ __('notifications.no_active_users') }}</p>
                    @endif
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.type') }}</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="system">{{ __('notifications.type_system') }}</option>
                        <option value="order">{{ __('notifications.type_order') }}</option>
                        <option value="promotion">{{ __('notifications.type_promotion') }}</option>
                    </select>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.title_label') }}</label>
                    <input 
                        type="text" 
                        name="title" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('notifications.message_label') }}</label>
                    <textarea 
                        name="message" 
                        rows="4"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    ></textarea>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-paper-plane ml-2"></i>
                    {{ __('notifications.send_submit') }}
                </button>
                <button type="button" onclick="closeSendModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition">
                    {{ __('common.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showSendModal() {
    document.getElementById('sendModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.add('hidden');
}

function toggleUserSelect(value) {
    const userSelect = document.getElementById('userSelect');
    if (value === 'specific') {
        userSelect.classList.remove('hidden');
    } else {
        userSelect.classList.add('hidden');
    }
}

function markAsRead(notificationId) {
    fetch(`/admin/notifications/${encodeURIComponent(notificationId)}/mark-read`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteNotification(notificationId) {
    if (!confirm('{{ __("notifications.confirm_delete_notification") }}')) {
        return;
    }

    fetch(`/admin/notifications/${encodeURIComponent(notificationId)}`, {
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
            alert(data.message || '{{ __("notifications.error_occurred") }}');
        }
    })
    .catch(error => {
        alert('{{ __("notifications.error_occurred") }}');
        console.error('Error:', error);
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSendModal();
    }
});
</script>
@endpush

