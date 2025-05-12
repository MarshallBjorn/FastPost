@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Postmat</h1>

    @if ($errors->any())
        <h1 class="mt-2 text-xl text-center text-red-600">Errors!</h1>
        <div class="text-black p-4 mb-4 border-dotted border-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('postmats.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label>Name</label>
            <input type="text" name="name" placeholder="Name" class="form-input" required>
        </div>

        <div>
            <label>City</label>
            <input type="text" name="city" placeholder="City" class="form-input" required>
        </div>

        <div>
            <label>Post Code</label>
            <input type="text" name="post_code" placeholder="Post Code" class="form-input" required>
        </div>

        <div>
            <label>Latitude</label>
            <input type="text" name="latitude" placeholder="Latitude" class="form-input" required>
        </div>

        <div>
            <label>Longitude</label>
            <input type="text" name="longitude" placeholder="Longitude" class="form-input" required>
        </div>

        <div>
            <label>Pick Location</label>
            <div id="map" style="height: 300px;" class="rounded border mt-2 mb-4"></div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Set default map center and zoom
                const map = L.map('map').setView([51.505, -0.09], 13); // Example coords

                // Load OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Marker initialization
                let marker = null;

                // On map click
                map.on('click', function (e) {
                    const { lat, lng } = e.latlng;

                    // Update input fields
                    document.querySelector('input[name="latitude"]').value = lat.toFixed(6);
                    document.querySelector('input[name="longitude"]').value = lng.toFixed(6);

                    // Remove existing marker
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // Add marker to clicked location
                    marker = L.marker([lat, lng]).addTo(map);
                });
            });
        </script>

        <div>
            <label>Status</label>
            <select name="status" class="form-input" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="unavailable">Unavailable</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <button class="form-submit">Create</button>
    </form>
</div>
@endsection