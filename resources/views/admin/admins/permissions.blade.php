@extends('layouts.admin')

@section('title', __('admin.admins.manage_permissions_title'))

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
                <a href="{{ route('admin.admins.index') }}" class="text-gray-600 hover:text-indigo-600">{{ __('sidebar.admins') }}</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                <span class="text-gray-500">{{ __('admin.admins.manage_permissions_title') }}</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.admins.manage_permissions_title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('admin.admins.manage_permissions_subtitle') }}</p>
    </div>
    <div>
        <a href="{{ route('admin.permission-groups.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
            <i class="fas fa-plus"></i>
            <span>{{ __('admin.admins.create_group') }}</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- مجموعات الصلاحيات -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">{{ __('admin.admins.permission_groups') }}</h2>
            <span class="text-sm text-gray-500">{{ $permissionGroups->count() }} {{ __('admin.admins.group') }}</span>
        </div>
        
        <div class="space-y-3 max-h-[600px] overflow-y-auto">
            @forelse($permissionGroups as $group)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $group->getName() }}</h3>
                        @if($group->getDescription())
                        <p class="text-sm text-gray-500 mt-1">{{ $group->getDescription() }}</p>
                        @endif
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-key ml-1"></i>
                                {{ $group->permissions->count() }} {{ __('admin.admins.permissions_count') }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $group->is_active ? __('admin.admins.active') : __('admin.admins.inactive') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2 mr-3">
                        <a href="{{ route('admin.permission-groups.edit', $group) }}" class="text-blue-600 hover:text-blue-800" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.permission-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($group->permissions->count() > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-700 mb-2">الصلاحيات:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($group->permissions->take(5) as $permission)
                        <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs rounded">
                            {{ $permission->name }}
                        </span>
                        @endforeach
                        @if($group->permissions->count() > 5)
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                            +{{ $group->permissions->count() - 5 }} أكثر
                        </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-layer-group text-4xl mb-3 text-gray-300"></i>
                <p>{{ __('admin.admins.no_permission_groups') }}</p>
                <a href="{{ route('admin.permission-groups.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">
                    {{ __('admin.admins.create_group_link') }}
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- الصلاحيات الفردية -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">{{ __('admin.admins.individual_permissions') }}</h2>
            <span class="text-sm text-gray-500">{{ collect($permissions)->sum(fn($p) => $p->count()) }} {{ __('admin.admins.no_permissions') }}</span>
        </div>
        
        <div class="space-y-4 max-h-[600px] overflow-y-auto">
            @foreach($permissions ?? [] as $category => $categoryPermissions)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800 capitalize">{{ $category }}</h3>
                    <span class="text-xs text-gray-500">{{ $categoryPermissions->count() }} صلاحية</span>
                </div>
                <div class="space-y-2">
                    @foreach($categoryPermissions as $permission)
                    <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-shield-alt ml-1"></i>
                            {{ $permission->guard_name }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
