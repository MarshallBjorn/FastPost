@extends('layouts.public')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="bg-white shadow-xl rounded-xl p-8 max-w-2xl w-full space-y-6">
            <h1 class="text-3xl font-bold text-center text-blue-800">Tracking History</h1>

            @if ($not_exist)
                <div class="text-center text-red-500 text-xl font-semibold mt-10">
                    Package not found.
                </div>
            @else
                @if($maskedEmail || $maskedPhone)
                    <div class="text-center my-6">
                        @if($maskedEmail)
                            <p class="text-gray-700 text-md">
                                Reciever Email: <strong>{{ $maskedEmail }}</strong>
                            </p>
                        @endif
                        @if($maskedPhone)
                            <p class="text-gray-700 text-md">
                                Reciever Phone: <strong>{{ $maskedPhone }}</strong>
                            </p>
                        @endif
                    </div>
                @endif

                @if(isset($error))
                    <div class="text-center text-red-500 font-semibold">{{ $error }}</div>
                @elseif($actualizations->isEmpty())
                    <p class="text-gray-500 text-center">No tracking updates available for this package yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($actualizations as $entry)
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

                <div class="text-center mt-6">
                    <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline text-sm">← Back</a>
                </div>

                <div class="mt-10">
                    <h2 class="text-2xl font-semibold text-blue-800 mb-4 text-center">Delivery Destination</h2>
                    <div id="map" class="w-full h-[400px] rounded-lg shadow"></div>
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
            document.addEventListener('DOMContentLoaded', function () {
                const lat = {{ $postmat->latitude }};
                const lng = {{ $postmat->longitude }};

                const map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup("<strong>{{ $postmat->name }}</strong><br>{{ $postmat->city }}, {{ $postmat->{'post-code'} }}")
                    .openPopup();
            });
        </script>
    @endpush
@endif