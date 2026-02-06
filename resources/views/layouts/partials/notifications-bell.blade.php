@php
    $isAdmin = auth()->guard('admin')->check();
    $currentUser = $isAdmin ? auth()->guard('admin')->user() : auth()->user();
    $notifications = $currentUser ? $currentUser->notifications()->limit(10)->get() : collect();
    $unreadCount = $currentUser ? $currentUser->unreadNotifications()->count() : 0;
    $allUrl = $isAdmin ? route('admin.notifications.index') : route('my.notifications.index');
    $markReadBase = $isAdmin ? url('admin/notifications') : url('my/notifications');
@endphp
@if($currentUser)
<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
    <button type="button"
            @click="open = !open"
            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            aria-label="{{ __('sidebar.notifications') ?? 'الإشعارات' }}">
        <i class="fas fa-bell text-lg"></i>
        @if($unreadCount > 0)
        <span class="absolute top-1 {{ app()->getLocale() === 'ar' ? 'left-1' : 'right-1' }} flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 w-80 sm:w-96 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-2 {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} max-h-[min(24rem,70vh)] flex flex-col"
         style="display: none;">
        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between shrink-0">
            <span class="font-semibold text-gray-900">{{ __('sidebar.notifications') ?? 'الإشعارات' }}</span>
            <a href="{{ $allUrl }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('common.view_all') ?? 'عرض الكل' }}</a>
        </div>
        <div class="overflow-y-auto flex-1 min-h-0">
            @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $title = $data['title'] ?? 'إشعار';
                $message = $data['message'] ?? '';
                $type = $data['type'] ?? 'system';
            @endphp
            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 {{ $notification->read_at ? 'opacity-80' : 'bg-indigo-50/50' }}" data-notification-id="{{ $notification->id }}">
                <div class="flex gap-3 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center
                        @if($type === 'order') bg-blue-100 text-blue-600
                        @elseif($type === 'system') bg-purple-100 text-purple-600
                        @elseif($type === 'design_created') bg-indigo-100 text-indigo-600
                        @else bg-orange-100 text-orange-600
                        @endif">
                        <i class="fas text-sm
                            @if($type === 'order') fa-shopping-cart
                            @elseif($type === 'system') fa-cog
                            @elseif($type === 'design_created') fa-palette
                            @else fa-bullhorn
                            @endif
                        "></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                    <button type="button" onclick="markNotificationRead('{{ $notification->id }}', '{{ $markReadBase }}')" class="flex-shrink-0 text-indigo-600 hover:text-indigo-800 text-xs" title="{{ __('common.mark_read') ?? 'تعليم كمقروء' }}">
                        <i class="fas fa-check-double"></i>
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-bell-slash text-3xl mb-2"></i>
                <p class="text-sm">{{ __('common.no_notifications') ?? 'لا توجد إشعارات' }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@push('scripts')
<script>
function markNotificationRead(id, baseUrl) {
    fetch(baseUrl + '/' + encodeURIComponent(id) + '/mark-read', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) window.location.reload(); })
    .catch(() => {});
}
</script>
@endpush
@endif
