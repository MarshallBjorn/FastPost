
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
                        <p><span class="font-medium">Name:</span> {{ $package->receiver->name }}</p>
                        <p><span class="font-medium">Email:</span> {{ $package->receiver_email }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $package->receiver_phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div class="mt-6 border-t pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Package Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="font-medium">Package ID</p>
                        <p class="text-gray-600">{{ $package->id }}</p>
                    </div>
                    <div>
                        <p class="font-medium">Status</p>
                        <p class="text-gray-600 capitalize">{{ $package->status }}</p>
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
                        <p class="font-medium">Sent At</p>
                        <p class="text-gray-600">{{ $package->sent_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="mt-8 flex flex-col items-center">
                <h3 class="text-lg font-medium mb-4">Package QR Code</h3>
                <img src="data:image/png;base64, {{ $qrCode }}" alt="Package QR Code" class="w-48 h-48 border p-2 rounded-lg">
                <p class="mt-2 text-sm text-gray-500">Scan this QR code to track your package</p>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-center">
                <a href="/" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
