@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Postmat</h1>

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

    <form method="POST" action="{{ route('postmats.store') }}" class="space-y-4">
        @csrf

        <label class="">Name</label>
        <input type="text" class="form-input" name="name" value="{{ old('name') }}" required>
        @error('name')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label class="">City</label>
        <input type="text" class="form-input" name="city" value="{{ old('city') }}" required>
        @error('city')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label class="">Post Code</label>
        <input type="text" class="form-input" name="post_code" value="{{ old('post_code') }}" required>
        @error('post_code')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label class="">Latitude</label>
        <input type="text" class="form-input" name="latitude" value="{{ old('latitude') }}" required>
        @error('latitude')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label class="">Longitude</label>
        <input type="text" class="form-input" name="longitude" value="{{ old('longitude') }}" required>
        @error('longitude')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label class="">Status</label>
        <select name="status" class="form-input" required>
            @foreach (['active', 'unavailable', 'maintenance'] as $status)
                <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

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

        <button class="form-submit">Create</button>
    </form>
</div>
@endsection