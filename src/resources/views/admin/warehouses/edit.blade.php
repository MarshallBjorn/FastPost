@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Warehouse</h2>

    <form method="POST" action="{{ route('warehouses.update', $warehouse) }}">
        @csrf
        @method('PUT')

        <label class="">City</label>
        <input type="text" class="form-input" name="city" value="{{ old('city', $warehouse->city) }}" required>

        <label class="">Post Code</label>
        <input type="text" class="form-input" name="post_code" value="{{ old('post_code', $warehouse->post_code) }}" required>

        <label class="">Latitude</label>
        <input type="text" class="form-input" name="latitude" value="{{ old('latitude', $warehouse->latitude) }}" required>

        <label class="">Longitude</label>
        <input type="text" class="form-input" name="longitude" value="{{ old('longitude', $warehouse->longitude) }}" required>

        <label class="">Status</label>
        <select name="status" class="form-input" required>
            @foreach (['active', 'unavailable', 'maintenance'] as $status)
                <option value="{{ $status }}" @selected($warehouse->status === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <input type="hidden" name="connections" id="connections-json">

        <div class="flex items-center justify-between mt-4">
            <button type="submit" class="form-submit border-green-700 bg-green-200">Update</button>
            <a href="{{ route('warehouses.index') }}" class="form-submit">Cancel</a>
        </div>
    </form>

    <div class="mt-4">
        <h2 class="text-xl font-bold mb-4">Delivery Destination</h2>
        <div id="map" class="w-full h-[400px] rounded-lg shadow"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $warehouse->latitude }};
        const lng = {{ $warehouse->longitude }};
        const currentWarehouseId = {{ $warehouse->id }};
        const warehouses = @json($all_warehouses);
        const existingConnections = @json($connections);
        const allConnections = @json($all_connections);

        const warehouseMap = {};
        warehouses.forEach(w => warehouseMap[w.id] = w);
        warehouseMap[currentWarehouseId] = {
            id: currentWarehouseId,
            latitude: lat,
            longitude: lng,
            city: "{{ $warehouse->city }}",
            post_code: "{{ $warehouse->post_code }}"
        };

        const selectedConnectionKeys = new Set();

        // Initialize with existing connections for this warehouse
        existingConnections.forEach(conn => {
            const ids = [conn.from_warehouse_id, conn.to_warehouse_id].sort((a, b) => a - b);
            selectedConnectionKeys.add(`${ids[0]}-${ids[1]}`);
        });

        const map = L.map('map').setView([lat, lng], 6);
        const lines = {}; // lines tied to each other warehouse

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Draw all connections (gray)
        allConnections.forEach(conn => {
            const w1 = warehouseMap[conn.from_warehouse_id];
            const w2 = warehouseMap[conn.to_warehouse_id];
            if (!w1 || !w2) return;

            L.polyline([[w1.latitude, w1.longitude], [w2.latitude, w2.longitude]], {
                color: 'blue',
                weight: 1,
            }).addTo(map);
        });

        // Draw main warehouse marker
        L.marker([lat, lng])
            .addTo(map)
            .bindPopup("{{ $warehouse->city }}, {{ $warehouse->post_code }}")
            .openPopup();

        // Draw other markers and make clickable
        warehouses.forEach(w => {
            const marker = L.marker([w.latitude, w.longitude])
                .addTo(map)
                .bindPopup(`${w.city}, ${w.post_code}`);

            const ids = [currentWarehouseId, w.id].sort((a, b) => a - b);
            const key = `${ids[0]}-${ids[1]}`;

            if (selectedConnectionKeys.has(key)) {
                const line = L.polyline([[lat, lng], [w.latitude, w.longitude]], {
                    color: 'green',
                    weight: 3,
                }).addTo(map);
                lines[w.id] = line;
            }

            // Hover line preview
            marker.on('mouseover', function () {
                if (lines[w.id]) return;
                lines[w.id] = L.polyline([[lat, lng], [w.latitude, w.longitude]], {
                    color: 'orange',
                    dashArray: '4',
                    weight: 2,
                }).addTo(map);
            });

            marker.on('mouseout', function () {
                if (selectedConnectionKeys.has(key)) return;
                if (lines[w.id]) {
                    map.removeLayer(lines[w.id]);
                    delete lines[w.id];
                }
            });

            // Click to toggle
            marker.on('click', function () {
                if (selectedConnectionKeys.has(key)) {
                    selectedConnectionKeys.delete(key);
                    if (lines[w.id]) {
                        map.removeLayer(lines[w.id]);
                        delete lines[w.id];
                    }
                } else {
                    selectedConnectionKeys.add(key);
                    const line = L.polyline([[lat, lng], [w.latitude, w.longitude]], {
                        color: 'green',
                        weight: 3,
                    }).addTo(map);
                    lines[w.id] = line;
                }
            });
        });

        // On form submit, serialize selected keys
        document.querySelector('form').addEventListener('submit', function () {
            const json = JSON.stringify(Array.from(selectedConnectionKeys));
            document.getElementById('connections-json').value = json;
        });
    });
    </script>
</div>
@endsection
