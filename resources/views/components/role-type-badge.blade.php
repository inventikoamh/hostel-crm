@props(['is_system'])

@if($is_system)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
        <i class="fas fa-cog mr-1"></i>
        System
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
        <i class="fas fa-user-plus mr-1"></i>
        Custom
    </span>
@endif
