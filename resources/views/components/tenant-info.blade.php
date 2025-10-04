@props(['name', 'email', 'avatar'])

<div class="flex items-center">
    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
        @if(isset($avatar) && $avatar)
            <img src="{{ asset('storage/' . $avatar) }}" alt="{{ $name }}" class="w-10 h-10 rounded-full object-cover">
        @else
            <i class="fas fa-user text-gray-600"></i>
        @endif
    </div>
    <div>
        <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $name }}</div>
        <div class="text-sm" style="color: var(--text-secondary);">{{ $email }}</div>
    </div>
</div>
