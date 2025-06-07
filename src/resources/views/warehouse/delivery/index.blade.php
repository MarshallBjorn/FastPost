@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">📍 Available Delivery Routes</h1>

    @if (session('status'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-6">
            {{ session('status') }}
        </div>
    @endif

    @forelse ($routes as $key => $route)
        <div class="bg-white shadow rounded-lg p-5 mb-6">
            <div class="flex justify-between items-center mb-3">
                <div class="text-lg font-semibold">
                    {{ $route['from']->city }} → {{ $route['to']->city }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $route['distance'] ?? 'N/A' }} km
                </div>
            </div>

            <p><strong>Packages to deliver:</strong> {{ $route['count'] }}</p>
            <p><strong>Packages to return:</strong> {{ $route['return_count'] ?? 0 }}</p>

            {{-- Take Route --}}
            <form action="{{ route('warehouse.delivery.take', [$route['from']->id, $route['to']->id]) }}" method="POST" class="mt-4">
                @csrf
                @foreach ($route['packages'] as $package)
                    <input type="hidden" name="packages[]" value="{{ $package->id }}">
                @endforeach
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    🚚 Take Route
                </button>
            </form>

            {{-- Confirm Arrival --}}
            <form action="{{ route('warehouse.delivery.confirm_arrival', [$route['from']->id, $route['to']->id]) }}" method="POST" class="mt-2">
                @csrf
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    ✅ Confirm Arrival
                </button>
            </form>

            {{-- Confirm Return --}}
            <form action="{{ route('warehouse.delivery.confirm_return', [$route['to']->id, $route['from']->id]) }}" method="POST" class="mt-2">
                @csrf
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    🔄 Confirm Return
                </button>
            </form>
        </div>
    @empty
        <p class="text-gray-500">No delivery routes available.</p>
    @endforelse

    {{-- Start Return to Mother Warehouse --}}
    <form action="{{ route('warehouse.delivery.start_return') }}" method="POST" class="mt-6">
        @csrf
        <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
            🏠 Start Return Trip to Mother Warehouse
        </button>
    </form>

    <div class="text-right mt-8">
        <a href="{{ route('warehouse.delivery.my_packages') }}"
           class="text-blue-600 underline">📦 View My Packages</a>
    </div>
</div>
@endsection