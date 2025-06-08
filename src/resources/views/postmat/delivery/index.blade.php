@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="grid grid-cols-2">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Today's Pickups</h1>
        <div class="mb-6 text-right">
            <a href="{{ route('postmat.delivery.my_packages') }}"
            class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                📦 View My Current Packages
            </a>
        </div>
    </div>
    <p class="text-gray-600 mb-6">
        The following postmats are scheduled for package drop-off and pick-up.
    </p>

    @if ($postmatRoutes->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($postmatRoutes as $route)
                <div class="bg-white p-4 rounded shadow hover:shadow-lg transition-shadow">
                    <h2 class="text-xl font-semibold text-blue-700 mb-2">
                        {{ $route['postmat']->name }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        <strong>City:</strong> {{ $route['postmat']->city }}<br>
                        <strong>Post Code:</strong> {{ $route['postmat']->post_code }}<br>
                        <strong>Status:</strong>
                        <span class="{{ $route['postmat']->status === 'active' ? 'text-green-600' : 'text-red-500' }}">
                            {{ ucfirst($route['postmat']->status) }}
                        </span>
                    </p>
                    <p class="mt-2 text-sm">
                        📥 <strong>{{ $route['pickup_count'] }}</strong> pickup(s) to warehouse<br>
                        📤 <strong>{{ $route['delivery_count'] }}</strong> delivery(ies) to postmat<br>
                        🧭 <strong>{{ number_format($route['distance'], 1) }} km</strong> distance
                    </p>
                    <form action="{{ route('postmat.delivery.pickup', ['postmat' => $route['postmat']->id]) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            Take In/Out Packages
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">No postmats have packages to move today.</p>
    @endif
</div>
@endsection
