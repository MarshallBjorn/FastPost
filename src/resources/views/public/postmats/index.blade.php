@extends('layouts.public')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Browse Postmats</h1>
    <p class="text-gray-600 mt-1">Find nearby postmats with status and location info.</p>
</div>

<!-- Filter + Sort Controls -->
<form method="GET" class="flex flex-col rounded shadow hover:shadow-lg bg-white justify-center p-3 mb-6 w-1/2 mx-auto">
    <div class="flex flex-row justify-center sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0 mb-2">
        <div class="p-2">
            <label for="city" class="ml-2 block text-sm font-medium text-gray-700 mb-1">City</label>
            <input type="text" name="city" id="city" value="{{ request('city') }}" placeholder="Search city..."
                   class="form-input w-full rounded-md shadow-sm px-3 py-2 border border-gray-300">
        </div>
    
        <div class="p-2">
            <label for="status" class="ml-2 block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2">
                <option value="">Any</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="unavailable" @selected(request('status') === 'unavailable')>Unavailable</option>
                <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
            </select>
        </div>
    
        <div class="p-2">
            <label for="sort" class="ml-2 block text-sm font-medium text-gray-700 mb-1">Sort By</label>
            <select name="sort" id="sort" class="form-select w-full rounded-md border border-gray-300 shadow-sm px-3 py-2">
                <option value="name_asc" @selected(request('sort') === 'name_asc')>Name ↑</option>
                <option value="name_desc" @selected(request('sort') === 'name_desc')>Name ↓</option>
                <option value="city_asc" @selected(request('sort') === 'city_asc')>City ↑</option>
                <option value="city_desc" @selected(request('sort') === 'city_desc')>City ↓</option>
            </select>
        </div>
    </div>

    <div class="p-2 w-1/2 flex gap-2">
        <button type="submit"
                class="w-full inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow">
            Apply
        </button>
        <button type="submit"
                class="w-full inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow">
            Reset
        </button>
    </div>
</form>

<!-- Postmat Grid -->
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($postmats as $postmat)
    <div class="bg-white p-4 rounded shadow hover:shadow-lg transition cursor-pointer"
            data-lat="{{ $postmat->latitude }}"
            data-lng="{{ $postmat->longitude }}"
            data-id="{{ $postmat->id }}" >
            <h3 class="text-lg font-semibold text-blue-700">{{ $postmat->name }}</h3>
            <p class="text-sm text-gray-600">{{ $postmat->city }}, {{ $postmat->{'post-code'} }}</p>
            <p class="text-sm mt-1">
                <span class="font-medium">Status:</span>
                <span class="@if($postmat->status === 'active') text-green-600 @elseif($postmat->status === 'maintenance') text-yellow-600 @else text-red-600 @endif">
                    {{ ucfirst($postmat->status) }}
                </span>
            </p>
            <div class="mt-2 text-xs text-gray-400">
                Lat: {{ $postmat->latitude }}, Lng: {{ $postmat->longitude }}
            </div>
        </div>
    @empty
        <p class="text-gray-500">No postmats found matching your criteria.</p>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $postmats->withQueryString()->links() }}
</div>

<!-- Map Container -->
<div class="mt-10">
    <h2 class="text-xl font-semibold mb-4">Map View</h2>
    <div id="postmat-map" class="w-full h-[500px] rounded shadow"></div>
</div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize the map
            const map = L.map('postmat-map').setView([52.237, 21.017], 6); // Poland default center

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Get postmats data
            const postmats = @json($allPostmats);
            const markers = {};

            // Add markers to the map
            postmats.forEach(pm => {
                if (pm.latitude && pm.longitude && !isNaN(pm.latitude) && !isNaN(pm.longitude)) {
                    const marker = L.marker([pm.latitude, pm.longitude])
                        .addTo(map)
                        .bindPopup(`<strong>${pm.name}</strong><br>${pm.city}<br>Status: ${ucfirst(pm.status)}`);
                    markers[pm.id] = marker;
                }
            });

            // Add click event listeners to postmat cards
            document.querySelectorAll('[data-lat][data-lng][data-id]').forEach(card => {
                card.addEventListener('click', () => {
                    const lat = parseFloat(card.dataset.lat);
                    const lng = parseFloat(card.dataset.lng);
                    const id = card.dataset.id;

                    if (!isNaN(lat) && !isNaN(lng) && markers[id]) {
                        map.setView([lat, lng], 14); // Zoom in
                        markers[id].openPopup();
                    }
                });
            });
        });

        // Helper function to capitalize status
        function ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endpush
