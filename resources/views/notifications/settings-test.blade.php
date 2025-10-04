@extends('layouts.app')

@section('title', 'Notification Settings Test')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Notification Settings Test</h1>

        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-2">Debug Information:</h2>
            <p><strong>Stats:</strong> {{ isset($stats) ? 'Available' : 'Not Available' }}</p>
            <p><strong>Data:</strong> {{ isset($data) ? 'Available (' . count($data) . ' items)' : 'Not Available' }}</p>
            <p><strong>Columns:</strong> {{ isset($columns) ? 'Available (' . count($columns) . ' items)' : 'Not Available' }}</p>
            <p><strong>Filters:</strong> {{ isset($filters) ? 'Available (' . count($filters) . ' items)' : 'Not Available' }}</p>
            <p><strong>Bulk Actions:</strong> {{ isset($bulkActions) ? 'Available (' . count($bulkActions) . ' items)' : 'Not Available' }}</p>
            <p><strong>Settings:</strong> {{ isset($settings) ? 'Available' : 'Not Available' }}</p>
        </div>

        @if(isset($stats))
        <div class="mt-4 bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-2">Stats:</h2>
            <pre>{{ json_encode($stats, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif

        @if(isset($data))
        <div class="mt-4 bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-2">Data (first 3 items):</h2>
            <pre>{{ json_encode(array_slice($data, 0, 3), JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>
@endsection


