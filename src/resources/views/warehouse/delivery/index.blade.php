@extends('layouts.public')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <div class="grid grid-cols-2">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Interwarehouse Routes</h1>
            <div class="mb-6 text-right">
                <a href="{{ route('warehouse.delivery.my_packages') }}"
                class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    📦 View My Current Packages
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 border border-green-300">
                {{ session('status') }}
            </div>
        @endif

        @forelse($routes as $key => $route)
            <div class="border rounded p-4 mb-4 shadow bg-white">
                <div class="text-lg font-semibold mb-2">
                    {{ $route['from']->city }} → {{ $route['to']->city }}
                </div>
                <p><strong>Packages:</strong> {{ $route['count'] }}</p>
                <p><strong>Return Packages:</strong> {{ $route['return_count'] ?? 0 }}</p>
                <p><strong>Distance:</strong> {{ $route['distance'] ?? 'Unknown' }} km</p>
                <form action="{{ route('warehouse.delivery.take', [$route['from']->id, $route['to']->id]) }}" method="POST" class="mt-3">
                    @csrf
                    @foreach ($route['packages'] as $package)
                        <input type="hidden" name="packages[]" value="{{ $package->id }}">
                    @endforeach
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Take Order</button>
                </form>
            </div>
        @empty
            <p>No outgoing routes at the moment.</p>
        @endforelse
    </div>
@endsection
