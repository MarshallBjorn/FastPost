@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Current Packages</h1>

    @if (count($packages) > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach ($packages as $package)
                <div class="bg-white p-4 rounded shadow">
                    <p><strong>ID:</strong> {{ $package->id }}</p>
                    <p><strong>Status:</strong> {{ $package->status }}</p>
                    <p><strong>From Postmat:</strong> {{ optional($package->startPostmat)->name ?? '-' }}</p>
                    <p><strong>To:</strong> {{ $package->endPostmat->name ?? '-' }}</p>
                    <p><strong>Route:</strong>
                        @php
                            $route = json_decode(optional($package->latestActualization)->route_remaining, true);
                            echo $route ? implode(' → ', $route) : 'Final';
                        @endphp
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">You currently have no packages assigned.</p>
    @endif
</div>
@endsection
