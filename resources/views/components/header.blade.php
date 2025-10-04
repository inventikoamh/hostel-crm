<!-- Header -->
<div class="mb-4 md:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center">
            @if($showBackButton && $backUrl)
                <a href="{{ $backUrl }}" class="mr-3 sm:mr-4 text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-lg sm:text-xl"></i>
                </a>
            @endif
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2" style="color: var(--text-primary);">{{ $title }}</h1>
                <p class="text-sm sm:text-base" style="color: var(--text-secondary);">{{ $subtitle }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- No buttons needed - sidebar toggle is in sticky header and sidebar -->
        </div>
    </div>
</div>
