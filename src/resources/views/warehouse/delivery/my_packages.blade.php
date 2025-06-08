@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">📦 My Current Packages</h1>

    @if (session('status'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-6">
            {{ session('status') }}
        </div>
    @endif

    @if ($packages->isEmpty())
        <p class="text-gray-500">You have no active packages.</p>
    @else
        <ul class="bg-white shadow rounded-lg divide-y divide-gray-200">
            @foreach ($packages as $package)
                @php
                    $a = $package->latestActualization;
                    $route = json_decode($a?->route_remaining ?? $package->route_path, true);
                    $nextStop = $route[0]['to'] ?? '—';
                @endphp

                @php
                    $nextStop = $route[0]['to'] ?? null;
                    $nextWarehouse = is_numeric($nextStop) ? \App\Models\Warehouse::find($nextStop) : null;
                @endphp

                <li class="p-4">
                    <div class="font-semibold">#{{ $package->id }}</div>
                    <div class="text-sm text-gray-600">Status: {{ ucfirst($package->status->value) }}</div>
                    <div class="text-sm text-gray-600">Current: {{ $a?->currentWarehouse->city ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-600">Next Stop: {{ $nextWarehouse?->city ?? 'End' }}</div>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="text-left mt-6">
        <a href="{{ route('warehouse.delivery.index') }}"
           class="text-blue-600 underline">⬅️ Back to Routes</a>
    </div>
</div>
@endsection
