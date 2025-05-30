@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Today's Pickups</h1>
    <p class="text-gray-600 mb-6">
        Below are the postmats sending packages to your warehouse. Pick them up and bring them in.
    </p>

    @if (count($routes) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($routes as $route)
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
                        📦 <strong>{{ $route['count'] }}</strong> packages ready<br>
                        🧭 <strong>{{ number_format($route['distance'], 1) }} km</strong> from you
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">No packages are ready for pickup from postmats today.</p>
    @endif
</div>
@endsection
