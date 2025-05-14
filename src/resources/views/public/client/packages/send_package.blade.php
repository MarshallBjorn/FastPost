@extends('layouts.public')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Send parcel</h2>
    <form action="{{ route('client.send_package.submit') }}" method="POST">
        @csrf

        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
            <strong>Something went wrong:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('warning'))
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 border border-yellow-400 rounded">
            ⚠️ {{ session('warning') }}
        </div>
        @endif

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">E-mail receiver</label>
            <input type="email" name="email" id="email" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium">Contact number of receiver</label>
            <input type="text" name="phone" id="phone" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="start_postmat" class="block text-sm font-medium">Start Postmat (Sender)</label>
            <input type="text" name="start_postmat" id="start_postmat" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="destination_postmat" class="block text-sm font-medium">Postmat Destination (Receiver)</label>
            <input type="text" name="destination_postmat" id="destination_postmat" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="nameFilter" class="block text-sm font-medium">Filter Postmats by Name</label>
            <input type="text" id="nameFilter" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" placeholder="Type to filter by name...">
        </div>

        <div class="mb-4">
            <label for="cityFilter" class="block text-sm font-medium">Filter Postmats by City</label>
            <input type="text" id="cityFilter" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" placeholder="Type to filter by city...">
        </div>

        <div id="map" style="height: 400px;" class="my-4"></div>

        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            const map = L.map('map').setView([52.237049, 21.017532], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const postmats = @json($postmats);
            const markers = [];

            postmats.forEach(pm => {
                const marker = L.marker([pm.latitude, pm.longitude]).bindPopup(
                    `<strong>${pm.name}</strong><br>City: ${pm.city}<br>
                    <button type="button" class="text-white rounded-xl px-2 mt-2 py-1 bg-blue-600 hover:bg-blue-700" onclick="selectPostmat('${pm.name}', 'start_postmat')">Set as Start</button><br>
                    <button type="button" class="text-white rounded-xl px-2 mt-2 py-1 bg-blue-600 hover:bg-blue-700" onclick="selectPostmat('${pm.name}', 'destination_postmat')">Set as Destination</button>`
                );
                marker.pmName = pm.name.toLowerCase();
                marker.pmCity = pm.city ? pm.city.toLowerCase() : '';
                marker.addTo(map);
                markers.push(marker);
            });

            function selectPostmat(name, fieldId) {
                document.getElementById(fieldId).value = name;
            }

            function updateMarkers() {
                const nameFilter = document.getElementById('nameFilter').value.toLowerCase();
                const cityFilter = document.getElementById('cityFilter').value.toLowerCase();

                markers.forEach(marker => {
                    const nameMatch = marker.pmName.includes(nameFilter);
                    const cityMatch = marker.pmCity.includes(cityFilter);
                    const isVisible = nameMatch && cityMatch;

                    if (isVisible) {
                        marker.addTo(map);
                    } else {
                        map.removeLayer(marker);
                    }
                });
            }

            document.getElementById('nameFilter').addEventListener('input', updateMarkers);
            document.getElementById('cityFilter').addEventListener('input', updateMarkers);
        </script>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send</button>
    </form>
</div>
@endsection