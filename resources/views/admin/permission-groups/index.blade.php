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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($groups as $group)
    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:border-indigo-200 transition-colors">
        <!-- Header -->
        <div class="flex items-center justify-between gap-2 mb-3">
            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $group->getName() }}</h3>
            <span class="flex-shrink-0 px-2 py-0.5 rounded text-xs {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $group->is_active ? __('admin.permission_groups.active') : __('admin.permission_groups.inactive') }}
            </span>
        </div>
        
        @if($group->getDescription())
        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $group->getDescription() }}</p>
        @endif
        
        <div class="flex flex-wrap gap-1.5 mb-3">
            @foreach($group->permissions->take(4) as $permission)
            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ $permission->name }}</span>
            @endforeach
            @if($group->permissions->count() > 4)
            <span class="px-2 py-0.5 bg-gray-200 text-gray-500 rounded text-xs">+{{ $group->permissions->count() - 4 }}</span>
            @endif
        </div>
        
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
            <a href="{{ route('admin.permission-groups.edit', $group) }}" class="text-xs text-indigo-600 hover:text-indigo-800">{{ __('admin.permission_groups.edit') }}</a>
            <form action="{{ route('admin.permission-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.permission_groups.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:text-red-800">{{ __('admin.permission_groups.delete') }}</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-lg border border-gray-200 p-8 text-center">
        <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
        <h3 class="text-base font-semibold text-gray-700 mb-1">{{ __('admin.permission_groups.no_groups') }}</h3>
        <p class="text-sm text-gray-500 mb-4">{{ __('admin.permission_groups.no_groups_desc') }}</p>
        <a href="{{ route('admin.permission-groups.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            {{ __('admin.permission_groups.add_new') }}
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
