@props(['name' => '', 'phone' => '', 'avatar' => ''])

<div class="flex items-center">
    <div class="flex-shrink-0 h-10 w-10">
        @if(!empty($avatar))
            <img class="h-10 w-10 rounded-full" src="{{ Storage::url($avatar) }}" alt="{{ $name }}">
        @else
            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                <i class="fas fa-user text-gray-600 dark:text-gray-300"></i>
            </div>
        @endif
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $name ?: 'Unknown User' }}</div>
        <div class="text-sm" style="color: var(--text-secondary);">{{ $phone ?: 'No phone' }}</div>
    </div>
</div>
