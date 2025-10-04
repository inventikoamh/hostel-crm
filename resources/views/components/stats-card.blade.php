@props([
    'icon' => 'fas fa-chart-bar',
    'iconColor' => '#2563eb',
    'iconBg' => 'rgba(59, 130, 246, 0.1)',
    'title' => 'Title',
    'value' => '0',
    'subtitle' => 'Subtitle',
    'subtitleIcon' => 'fas fa-info'
])

<div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1" style="background-color: var(--card-bg); border-color: var(--border-color);">
    <div class="flex items-center justify-between mb-2 sm:mb-3">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $iconBg }};">
            <i class="{{ $icon }} text-base sm:text-lg" style="color: {{ $iconColor }};"></i>
        </div>
        <div class="text-right">
            <p class="text-xs font-medium text-gray-500 truncate" style="color: var(--text-secondary);">{{ $title }}</p>
            <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate" style="color: var(--text-primary);" title="{{ $value }}">{{ $value }}</p>
        </div>
    </div>
    <div class="flex items-center text-xs text-gray-500" style="color: var(--text-secondary);">
        <i class="{{ $subtitleIcon }} mr-1"></i>
        <span class="truncate">{{ $subtitle }}</span>
    </div>
</div>
