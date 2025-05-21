@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Package Summary</h1>
                <p class="text-blue-100">Your package has been successfully registered</p>
            </div>

            <!-- Package Info -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sender Info -->
                    <div class="border-b md:border-b-0 md:border-r pb-6 md:pb-0 md:pr-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sender Information</h2>
                        <div class="space-y-2">
                            <p><span class="font-medium">Name:</span> {{ $package->sender->name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Email:</span> {{ $package->sender->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Receiver Info -->
                    <div class="pb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Receiver Information</h2>
                        <div class="space-y-2">
                            <p><span class="font-medium">Email:</span> {{ $package->receiver_email }}</p>
                            <p><span class="font-medium">Phone:</span> {{ $package->receiver_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Package Details -->
                <div class="mt-6 border-t pt-6">
                    @if ($stashChanged)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Postmat Changed</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            No available stashes at <span
                                                class="font-semibold">{{ $originalStartPostmat->name }}</span>.
                                            Your package will be sent from <span
                                                class="font-semibold">{{ $package->startPostmat->name }}</span> instead. Stash is reserved for you.
                                        </p>
                                        @if (isset($distance) && $distance > 0)
                                            <p class="mt-1">The new location is {{ $distance }} km away from your
                                                original choice. Please see changes on the map</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mt-3">
                                <button
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-yellow-800 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    See changes on map
                                </button>
                            </div> --}}
                        </div>

                        <div id="postmatMapContainer" class="mt-4 rounded-lg overflow-hidden border border-gray-200 mb-3">
                            <div id="postmatMap" style="height:256px; width:100%;" class="h-64 w-full"></div>
                            <div class="bg-gray-50 px-4 py-2 text-sm text-gray-600 flex justify-between items-center">
                                <span>
                                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-1"></span> Original:
                                    {{ $originalStartPostmat->name }}
                                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-1 ml-3"></span> New:
                                    {{ $package->startPostmat->name }}
                                </span>
                                {{-- <button onclick="hidePostmatMap()" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button> --}}
                            </div>
                        </div>
                    @endif

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Package Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="font-medium">Package ID</p>
                            <p class="text-gray-600">{{ $package->id }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Start Postmat</p>
                            <p class="text-gray-600">{{ $package->startPostmat->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Destination Postmat</p>
                            <p class="text-gray-600">{{ $package->destinationPostmat->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Size</p>
                            <p class="text-gray-600">{{ $package->size ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Weight</p>
                            <p class="text-gray-600">{{ $package->weight ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Status</p>
                            <p class="text-gray-600 capitalize">{{ $package->status }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Sent At</p>
                            <p class="text-gray-600">{{ $package->sent_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="mt-8 flex flex-col items-center">
                    <h3 class="text-lg font-medium mb-4">Package QR Code</h3>
                    <img src="data:image/png;base64, {{ $qrCode }}" alt="Package QR Code"
                        class="w-48 h-48 border p-2 rounded-lg">
                    <p class="mt-2 text-sm text-gray-500">  Scan this QR code to track your package, or access the tracking page using the link below.</p>
                    <a href="{{ $trackUrl }}" class="text-blue-500 mt-2">{{ $trackUrl }}</a>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center">
                    <a href="/"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', initPostmatMap);

        function initPostmatMap() {
            // Blade variables – adjust if you’re outside Laravel
            const originalLat = {{ $originalStartPostmat->latitude }};
            const originalLng = {{ $originalStartPostmat->longitude }};
            const newLat = {{ $package->startPostmat->latitude }};
            const newLng = {{ $package->startPostmat->longitude }};

            const centerLat = (originalLat + newLat) / 2;
            const centerLng = (originalLng + newLng) / 2;

            const map = L.map('postmatMap').setView([centerLat, centerLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            L.marker([originalLat, originalLng], {
                icon: L.divIcon({
                    html: '<div style="width:12px;height:12px;border-radius:50%;background:#3B82F6;border:2px solid #fff;"></div>',
                    className: '',
                    iconSize: [12, 12]
                })
            }).addTo(map).bindPopup('Original: {{ $originalStartPostmat->name ?? 'Origin' }}');

            L.marker([newLat, newLng], {
                icon: L.divIcon({
                    html: '<div style="width:12px;height:12px;border-radius:50%;background:#EF4444;border:2px solid #fff;"></div>',
                    className: '',
                    iconSize: [12, 12]
                })
            }).addTo(map).bindPopup('New: {{ $package->startPostmat->name ?? 'New' }}');

            L.polyline(
                [
                    [originalLat, originalLng],
                    [newLat, newLng]
                ], {
                    color: '#6b7280',
                    dashArray: '5 5'
                }
            ).addTo(map);
        }
    </script>


@endsection
