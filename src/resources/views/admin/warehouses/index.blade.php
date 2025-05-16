@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Warehouses</h1>
        <a href="{{ route('warehouses.create') }}" class="form-submit px-4 py-2">+ Create Warehouse</a>
    </div>

    <div id="warehouse-map" class="w-full h-150 mb-6 rounded-lg border-2 border-dotted"></div>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="p-2">City</th>
                    <th class="p-2">Post-Code</th>
                    <th class="p-2">Latitude</th>
                    <th class="p-2">Longitude</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($warehouses as $warehouse)
                <tr class="border-t text-center">
                    <td class="p-2">{{ $warehouse->city }}</td>
                    <td class="p-2">{{ $warehouse->post_code }}</td>
                    <td class="p-2">{{ $warehouse->latitude }}</td>
                    <td class="p-2">{{ $warehouse->longitude }}</td>
                    <td class="p-2">{{ ucfirst($warehouse->status) }}</td>
                    <td class="p-2">
                        <a href="{{ route('warehouses.show', $warehouse) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-yellow-500">Edit</a> |
                        <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('warehouse-map').setView([52.0, 19.0], 6); // Center on Poland

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const warehouses = @json($warehouses);
            const postmats = @json($postmats);

            warehouses.forEach(warehouse => {
                const marker = L.circleMarker([warehouse.latitude, warehouse.longitude], {
                    radius: 6,
                    color: 'blue',
                    fillColor: 'blue',
                    fillOpacity: 0.8,
                }).addTo(map).bindPopup(`<b>${warehouse.city} ${warehouse.id}</b>`);

                // Draw connections
                (warehouse.connections_from || []).forEach(conn => {
                    const to = conn.to_warehouse;
                    if (to) {
                        L.polyline([
                            [warehouse.latitude, warehouse.longitude],
                            [to.latitude, to.longitude]
                        ], {
                            color: 'green',
                            weight: 2,
                            opacity: 0.7,
                        }).addTo(map);
                    }
                });
            });
        });
    </script>
</div>
@endsection
