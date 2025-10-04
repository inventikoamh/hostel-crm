@props(['status'])

@php
    $badgeClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'sent' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
        'scheduled' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-800',
        'verified' => 'bg-green-100 text-green-800',
        'unverified' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-green-100 text-green-800',
        'unpaid' => 'bg-red-100 text-red-800',
        'overdue' => 'bg-red-100 text-red-800',
        'draft' => 'bg-gray-100 text-gray-800',
        'completed' => 'bg-green-100 text-green-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
        'true' => 'bg-green-100 text-green-800',
        'false' => 'bg-red-100 text-red-800',
        '1' => 'bg-green-100 text-green-800',
        '0' => 'bg-red-100 text-red-800',
    ];

    $class = $badgeClasses[$status] ?? 'bg-gray-100 text-gray-800';
    $displayText = is_bool($status) ? ($status ? 'Yes' : 'No') : ucfirst($status);
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ $displayText }}
</span>
