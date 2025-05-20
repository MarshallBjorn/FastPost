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
            const currentId = {{ $warehouse->id }};
            const lat = {{ $warehouse->latitude }};
            const lng = {{ $warehouse->longitude }};
            const warehouses = @json($all_warehouses);
            const existingKeys = @json($connectedKeys);

            const selectedKeys = new Set(existingKeys);
            const map = L.map('map').setView([lat, lng], 13);
            const lines = {};

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup("{{ $warehouse->city }}, {{ $warehouse->post_code }}")
                .openPopup();

            warehouses.forEach(w => {
                const key = [currentId, w.id].sort((a, b) => a - b).join('-');
                const distance = haversineDistance(lat, lng, w.latitude, w.longitude).toFixed(2);

                const marker = L.marker([w.latitude, w.longitude])
                    .addTo(map)
                    .bindPopup(`<strong>${w.city}, ${w.post_code}</strong><br>Distance: ${distance} km`);

                if (selectedKeys.has(key)) {
                    lines[key] = L.polyline([[lat, lng], [w.latitude, w.longitude]], { color: 'blue' }).addTo(map);
                }

                marker.on('mouseover', function () {
                    marker.openPopup();
                });
                marker.on('mouseout', function () {
                    marker.closePopup();
                });

                marker.on('click', () => {
                    if (selectedKeys.has(key)) {
                        selectedKeys.delete(key);
                        if (lines[key]) {
                            map.removeLayer(lines[key]);
                            delete lines[key];
                        }
                    } else {
                        selectedKeys.add(key);
                        lines[key] = L.polyline([[lat, lng], [w.latitude, w.longitude]], { color: 'green' }).addTo(map);
                    }
                });
            });

            document.querySelector('form').addEventListener('submit', function () {
                document.getElementById('connections-json').value = JSON.stringify(Array.from(selectedKeys));
            });

            function haversineDistance(lat1, lon1, lat2, lon2) {
                const R = 6371; // Earth's radius in km
                const toRad = angle => angle * Math.PI / 180;

                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);

                const a = Math.sin(dLat / 2) ** 2 +
                        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                        Math.sin(dLon / 2) ** 2;

                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }
        });
    </script>
</div>
@endsection
