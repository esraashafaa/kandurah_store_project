@extends('layouts.admin')

@section('title', __('admin.permission_groups.title'))

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
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ __('admin.permission_groups.title') }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.permission_groups.title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('admin.permission_groups.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.permission-groups.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>{{ __('admin.permission_groups.add_new') }}</span>
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Groups Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($groups as $group)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
        <!-- Group Header -->
        <div class="bg-gradient-to-r {{ $group->is_active ? 'from-indigo-50 to-purple-50 border-r-4 border-indigo-500' : 'from-gray-50 to-gray-100 border-r-4 border-gray-400' }} p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 {{ $group->is_active ? 'bg-indigo-500' : 'bg-gray-400' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $group->getName() }}</h3>
                        <p class="text-sm text-gray-600">{{ $group->getName(app()->getLocale() === 'ar' ? 'en' : 'ar') }}</p>
                    </div>
                </div>
                @if($group->is_active)
                    <span class="inline-block px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">
                        <i class="fas fa-check-circle ml-1"></i>
                        {{ __('admin.permission_groups.active') }}
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-gray-400 text-white rounded-full text-xs font-semibold">
                        <i class="fas fa-times-circle ml-1"></i>
                        {{ __('admin.permission_groups.inactive') }}
                    </span>
                @endif
            </div>
            
            @if($group->getDescription())
            <p class="text-sm text-gray-700 mb-4">{{ $group->getDescription() }}</p>
            @endif
            
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-shield-alt"></i>
                <span class="font-semibold">{{ $group->permissions->count() }}</span>
                <span>{{ __('admin.permission_groups.permissions_count') }}</span>
            </div>
        </div>
        
        <!-- Permissions List -->
        <div class="p-4 bg-gray-50">
            <p class="text-sm font-semibold text-gray-700 mb-2">{{ __('admin.permission_groups.permissions') }}:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($group->permissions->take(5) as $permission)
                <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                    {{ $permission->name }}
                </span>
                @endforeach
                @if($group->permissions->count() > 5)
                <span class="inline-block px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs">
                    +{{ $group->permissions->count() - 5 }} {{ __('admin.permission_groups.more') }}
                </span>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        <div class="p-4 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('admin.permission-groups.edit', $group) }}" class="text-indigo-600 hover:text-indigo-800 px-3 py-2 rounded-lg hover:bg-indigo-50 transition">
                <i class="fas fa-edit ml-1"></i>
                {{ __('admin.permission_groups.edit') }}
            </a>
            <form action="{{ route('admin.permission-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.permission_groups.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 px-3 py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="fas fa-trash ml-1"></i>
                    {{ __('admin.permission_groups.delete') }}
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-700 mb-2">{{ __('admin.permission_groups.no_groups') }}</h3>
        <p class="text-gray-500 mb-6">{{ __('admin.permission_groups.no_groups_desc') }}</p>
        <a href="{{ route('admin.permission-groups.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>{{ __('admin.permission_groups.add_new') }}</span>
        </a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($groups->hasPages())
<div class="mt-6">
    {{ $groups->links() }}
</div>
@endif

@endsection
