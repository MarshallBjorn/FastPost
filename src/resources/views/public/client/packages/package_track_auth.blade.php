@extends('layouts.public') {{-- Assuming 'layouts.app' is your logged-in layout --}}

@section('content')
<div class="min-h-[70vh] flex items-center justify-center bg-gray-100 py-12 px-4">
    <div class="bg-white shadow-xl rounded-xl p-8 max-w-4xl w-full space-y-6">
        <h1 class="text-3xl font-bold text-center text-blue-800">Detailed Package Tracking</h1>

        @if ($not_exist)
            <div class="text-center text-red-500 text-xl font-semibold mt-10">
                Package not found.
            </div>
        @else
            {{-- Package Metadata --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                <div>
                    <h2 class="font-semibold text-blue-700 mb-1">Sender Info</h2>
                    <p><strong>Name:</strong> {{ $package->sender?->first_name }} {{ $package->sender?->last_name }}</p>
                    <p><strong>Email:</strong> {{ $package->sender?->email }}</p>
                    <p><strong>Phone:</strong> {{ $package->sender?->phone }}</p>
                </div>
                <div>
                    <h2 class="font-semibold text-blue-700 mb-1">Receiver Info</h2>
                    <p><strong>Email:</strong> {{ $package->receiver_email }}</p>
                    <p><strong>Phone:</strong> {{ $package->receiver_phone }}</p>
                </div>
                <div>
                    <h2 class="font-semibold text-blue-700 mb-1">Package Details</h2>
                    <p><strong>Status:</strong> {{ ucfirst($package->status) }}</p>
                    <p><strong>Size:</strong> {{ $package->size }}</p>
                    <p><strong>Weight:</strong> {{ $package->weight }} g</p>
                    <p><strong>Unlock Code:</strong> {{ $package->unlock_code }}</p>
                </div>
                <div>
                    <h2 class="font-semibold text-blue-700 mb-1">Dates</h2>
                    <p><strong>Sent At:</strong> {{ optional($package->created_at)->format('d M Y, H:i') }}</p>
                    <p><strong>Delivered:</strong> {{ optional($package->delivered_date)->format('d M Y, H:i') }}</p>
                    <p><strong>Collected:</strong> {{ optional($package->collected_date)->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <h2 class="font-semibold text-blue-700 mb-1">Postmats</h2>
                    <p><strong>Start:</strong> {{ $package->startPostmat?->name }} ({{ $package->startPostmat?->city }})</p>
                    <p><strong>Destination:</strong> {{ $package->destinationPostmat?->name }} ({{ $package->destinationPostmat?->city }})</p>
                </div>
            </div>

            <div class="mt-10">
                <h2 class="text-2xl font-semibold text-blue-800 mb-4 text-center">Tracking History</h2>
                @if($actualizations->isEmpty())
                    <p class="text-gray-500 text-center">No tracking updates available for this package yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($actualizations as $entry)
                            <li class="border-l-4 border-blue-600 pl-4 py-2 bg-blue-50 rounded shadow-sm">
                                <div class="text-lg font-semibold text-blue-800 capitalize">
                                    {{ str_replace('_', ' ', $entry->message) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y, H:i') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Collected Confirmation --}}
            @if ($package->status === 'collected' && $package->collected_date)
                <div class="mt-6 text-center text-green-700 font-semibold">
                    Package was successfully collected on {{ \Carbon\Carbon::parse($package->collected_date)->format('d M Y, H:i') }}.
                </div>
            @endif

            {{-- Map --}}
            <div class="mt-10">
                <h2 class="text-2xl font-semibold text-blue-800 mb-4 text-center">Delivery Destination</h2>
                <div id="map" class="w-full h-[400px] rounded-lg shadow"></div>
            </div>

            {{-- Back link --}}
            <div class="text-center mt-6">
                <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline text-sm">← Back</a>
            </div>
        @endif
    </div>
</div>
@endsection

@if (!$not_exist)
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const lat = {{ $postmat->latitude ?? '0' }};
                const lng = {{ $postmat->longitude ?? '0' }};

                const map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(
                        "<strong>{{ $postmat->name }}</strong><br>{{ $postmat->city }}, {{ $postmat->{'post-code'} }}"
                    )
                    .openPopup();
            });
        </script>
    @endpush
@endif
