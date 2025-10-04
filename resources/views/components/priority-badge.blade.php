@props(['priority' => 'medium'])

@php
    $priorityConfig = [
        'low' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-arrow-down'],
        'medium' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-minus'],
        'high' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-arrow-up'],
        'urgent' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle']
    ];

    $config = $priorityConfig[$priority] ?? $priorityConfig['medium'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
    <i class="{{ $config['icon'] }} mr-1"></i>
    {{ ucfirst($priority) }}
</span>
