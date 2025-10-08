@extends('layouts.app')

@section('title', 'System Health')

@php
    $title = 'System Health';
    $subtitle = 'Monitor system status and performance';
@endphp

@section('content')
<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
    </div>

    <!-- Health Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($health as $component => $status)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    @if($status['status'] === 'healthy')
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    @elseif($status['status'] === 'warning')
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                    @elseif($status['status'] === 'error')
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                    @else
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                    @endif
                    <h3 class="text-lg font-semibold capitalize" style="color: var(--text-primary);">{{ $component }}</h3>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    @if($status['status'] === 'healthy') bg-green-100 text-green-800
                    @elseif($status['status'] === 'warning') bg-yellow-100 text-yellow-800
                    @elseif($status['status'] === 'error') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ ucfirst($status['status']) }}
                </span>
            </div>
            <p class="text-sm" style="color: var(--text-secondary);">{{ $status['message'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- System Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Server Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Server Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">PHP Version</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Laravel Version</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Server Software</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Memory Limit</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ ini_get('memory_limit') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Max Execution Time</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ ini_get('max_execution_time') }}s</span>
                </div>
            </div>
        </div>

        <!-- Application Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Application Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Environment</span>
                    <span class="text-sm font-medium px-2 py-1 rounded-full
                        @if(app()->environment('production')) bg-green-100 text-green-800
                        @elseif(app()->environment('staging')) bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ app()->environment() }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Debug Mode</span>
                    <span class="text-sm font-medium px-2 py-1 rounded-full
                        @if(config('app.debug')) bg-red-100 text-red-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Cache Driver</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ config('cache.default') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Queue Driver</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ config('queue.default') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Session Driver</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ config('session.driver') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="clearCache()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-broom mr-2"></i>
                    Clear Cache
                </button>
                <button onclick="clearConfig()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-cog mr-2"></i>
                    Clear Config
                </button>
                <button onclick="clearRoutes()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-route mr-2"></i>
                    Clear Routes
                </button>
                <button onclick="clearViews()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>
                    Clear Views
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function clearCache() {
        if (confirm('Are you sure you want to clear the application cache?')) {
            fetch('/super-admin/clear-cache', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cache cleared successfully!');
                    location.reload();
                } else {
                    alert('Failed to clear cache: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error clearing cache: ' + error.message);
            });
        }
    }

    function clearConfig() {
        if (confirm('Are you sure you want to clear the configuration cache?')) {
            fetch('/super-admin/clear-config', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Configuration cache cleared successfully!');
                    location.reload();
                } else {
                    alert('Failed to clear config: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error clearing config: ' + error.message);
            });
        }
    }

    function clearRoutes() {
        if (confirm('Are you sure you want to clear the route cache?')) {
            fetch('/super-admin/clear-routes', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Route cache cleared successfully!');
                    location.reload();
                } else {
                    alert('Failed to clear routes: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error clearing routes: ' + error.message);
            });
        }
    }

    function clearViews() {
        if (confirm('Are you sure you want to clear the view cache?')) {
            fetch('/super-admin/clear-views', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('View cache cleared successfully!');
                    location.reload();
                } else {
                    alert('Failed to clear views: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error clearing views: ' + error.message);
            });
        }
    }
</script>
@endpush
