@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 border-2 border-dotted mt-12">
    <h1 class="text-3xl font-bold mb-4">Package Details</h1>

    <div class="mb-6">
        <div class="grid grid-cols-2 max-w-xl">

            <div class="font-semibold">Status:</div>
            <div>{{ $package->status }}</div>

            <div class="font-semibold">Sender Email:</div>
            <div>{{ $package->sender->email }}</div>

            <div class="font-semibold">Receiver Email:</div>
            <div>{{ $package->receiver_email }}</div>

            <div class="font-semibold">Receiver Phone:</div>
            <div>{{ $package->receiver_phone }}</div>

            <div class="font-semibold">Unlock code:</div>
            <div>{{ $package->unlock_code }}</div>

            <div class="font-semibold">Sender ID:</div>
            <div>{{ $package->sender_id }}</div>

            <div class="font-semibold">Receiver ID:</div>
            <div>{{ $package->receiver_id }}</div>

            <div class="font-semibold">Start Postmat ID:</div>
            <div>{{ $package->start_postmat_id }}</div>

            <div class="font-semibold">Destination Postmat ID:</div>
            <div>{{ $package->destination_postmat_id }}</div>

            <div class="font-semibold">Sent At:</div>
            <div>{{ $package->sent_at ?? 'N/A' }}</div>

            <div class="font-semibold">Delivered Date:</div>
            <div>{{ $package->delivered_date ?? 'N/A' }}</div>

            <div class="font-semibold">Collected Date:</div>
            <div>{{ $package->collected_date ?? 'N/A' }}</div>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4">Package Route</h2>
    <div id="map" class="w-full h-96 mb-6 rounded border"></div>

    <h1 class="text-2xl font-bold mb-4">Actualizations</h1>
    @if ($package->actualizations->isEmpty())
        <p>No actualizations found for this package.</p>
    @else
        <table class="table w-full overflow-x-auto">
            <thead class="text-left">
                <tr>
                    <th class="py-2 px-3">Message</th>
                    <th class="py-2 px-3">Courier</th>
                    <th class="py-2 px-3">Warehouse</th>
                    <th class="py-2 px-3">Created At</th>
                    <th class="py-2 px-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($package->actualizations as $actualization)
                    <tr class="border-t-2 border-dotted p-1">
                        <td class="px-3">{{ $actualization->message }}</td>
                        <td class="px-3">{{ $actualization->courier?->id ?? 'N/A' }}</td>
                        <td class="px-3">{{ $actualization->currentWarehouse?->city ?? 'N/A' }}</td>
                        <td class="px-3">{{ $actualization->created_at }}</td>
                        <td class="px-3">
                            <a href="{{ route('actualizations.show', $actualization) }}" class="text-blue-500">View</a> |
                            <a href="{{ route('actualizations.edit', $actualization) }}" class="text-yellow-500">Edit</a> |
                            <form action="{{ route('actualizations.destroy', $actualization) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete?')" class="text-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@php
    $warehouseData = collect($warehouses)->keyBy('id');

    $routePath = $routePath ?? [];
    $routeRemaining = $routeRemaining ?? [];

    $traveled = array_diff($routePath, $routeRemaining);

    $traveledCoords = array_map(function($id) use ($warehouseData) {
        return isset($warehouseData[$id])
            ? [$warehouseData[$id]->latitude, $warehouseData[$id]->longitude]
            : null;
    }, $traveled);

    $currentWarehouseId = $package->latestActualization->current_warehouse_id ?? null;

    if ($currentWarehouseId && isset($warehouseData[$currentWarehouseId])) {
        $currentCoord = [
            $warehouseData[$currentWarehouseId]->latitude,
            $warehouseData[$currentWarehouseId]->longitude
        ];

        // ✅ Append current warehouse to traveled path
        $traveledCoords[] = $currentCoord;

        // ✅ Prepend it to remaining as well to connect the lines smoothly
        array_unshift($routeRemaining, $currentWarehouseId);
    }

    // ✅ Remaining path now starts from current → next → ...
    $remainingCoords = array_map(function($id) use ($warehouseData) {
        return isset($warehouseData[$id])
            ? [$warehouseData[$id]->latitude, $warehouseData[$id]->longitude]
            : null;
    }, $routeRemaining);

    $markerPoints = array_map(function($id) use ($warehouseData) {
        return isset($warehouseData[$id])
            ? [
                'coords' => [$warehouseData[$id]->latitude, $warehouseData[$id]->longitude],
                'city' => $warehouseData[$id]->city
            ]
            : null;
    }, $routePath);

    $traveledCoords = array_values(array_filter($traveledCoords));
    $remainingCoords = array_values(array_filter($remainingCoords));
    $markerPoints = array_filter($markerPoints);
@endphp

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('map');

        const traveledCoords = @json($traveledCoords);
        const remainingCoords = @json($remainingCoords);
        const markerPoints = @json(array_values($markerPoints));

        const allCoords = traveledCoords.concat(remainingCoords);

        if (allCoords.length > 0) {
            map.setView(allCoords[0], 6);
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (traveledCoords.length > 1) {
            L.polyline(traveledCoords, {
                color: 'green',
                weight: 5
            }).addTo(map).bindPopup('Traveled Path');
        }

        if (remainingCoords.length > 1) {
            L.polyline(remainingCoords, {
                color: 'orange',
                dashArray: '5, 10',
                weight: 5
            }).addTo(map).bindPopup('Remaining Path');
        }

        markerPoints.forEach(point => {
            L.circleMarker(point.coords, {
                radius: 6,
                color: 'blue',
                fillOpacity: 0.8
            }).addTo(map).bindTooltip(point.city);
        });

        if (allCoords.length > 0) {
            const bounds = L.latLngBounds(allCoords);
            map.fitBounds(bounds, { padding: [20, 20] });
        }
    });
</script>
</div>
@endsection
