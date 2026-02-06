@extends('layouts.user')

@section('title', __('sidebar.notifications'))

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('sidebar.notifications') ?? 'إشعاراتي' }}</h1>
    <p class="text-gray-600 mt-1">عرض إشعاراتك فقط</p>
</div>

<div class="space-y-4">
    @forelse($notifications as $notification)
    @php
        $data = $notification->data ?? [];
        $title = $data['title'] ?? 'إشعار';
        $message = $data['message'] ?? '';
        $type = $data['type'] ?? 'system';
    @endphp
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition {{ $notification->read_at ? 'opacity-75' : '' }}">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                @if($type === 'order') bg-blue-100 text-blue-600
                @elseif($type === 'system') bg-purple-100 text-purple-600
                @elseif($type === 'design_created') bg-indigo-100 text-indigo-600
                @else bg-orange-100 text-orange-600
                @endif">
                <i class="fas text-xl
                    @if($type === 'order') fa-shopping-cart
                    @elseif($type === 'system') fa-cog
                    @elseif($type === 'design_created') fa-palette
                    @else fa-bullhorn
                    @endif
                "></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-900 text-lg">{{ $title }}</h3>
                @if(!$notification->read_at)
                <span class="inline-block px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full mt-1">جديد</span>
                @endif
                <p class="text-gray-700 mt-2">{{ $message }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$notification->read_at)
                <button onclick="markAsRead('{{ $notification->id }}')" class="text-green-600 hover:text-green-900 text-sm">
                    <i class="fas fa-check ml-1"></i>
                    تعليم كمقروء
                </button>
                @endif
                <button onclick="deleteNotification('{{ $notification->id }}')" class="text-red-600 hover:text-red-900 text-sm">
                    <i class="fas fa-trash ml-1"></i>
                    حذف
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد إشعارات</h3>
        <p class="text-gray-600">لم تصلك أي إشعارات بعد</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch('/my/notifications/' + encodeURIComponent(notificationId) + '/mark-read', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) window.location.reload(); })
    .catch(() => {});
}
function deleteNotification(notificationId) {
    if (!confirm('حذف هذا الإشعار؟')) return;
    fetch('/my/notifications/' + encodeURIComponent(notificationId), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) window.location.reload(); })
    .catch(() => {});
}
</script>
@endpush
