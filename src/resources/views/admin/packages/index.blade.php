@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Packages</h1>
        <a href="{{ route('packages.create') }}" class="form-submit px-4 py-2">+ Create Package</a>
    </div>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="p-2">ID</th>
                    <th class="p-2">Sender</th>
                    <th class="p-2">Receiver</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Destination</th>
                    <th class="p-2">Receiver_email</th>
                    <th class="p-2">Receiver_phone</th>
                    <th class="p-2">Sent at</th>
                    <th class="p-2">Delivered date</th>
                    <th class="p-2">Collected date</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                <tr class="border-t text-center">
                    <td class="p-2">{{ $package->id }}</td>
                    <td class="p-2">{{ $package->sender->name ?? '-' }}</td>
                    <td class="p-2">{{ $package->receiver->name ?? '-' }}</td>
                    <td class="p-2">{{ ucfirst($package->status) }}</td>
                    <td class="p-2">{{ $package->destinationPostmat->name ?? '-' }}</td>
                    <td class="p-2">{{ $package->receiver_email }}</td>
                    <td class="p-2">{{ $package->receiver_phone }}</td>
                    <td class="p-2">{{ $package->sent_at ?? '-' }}</td>
                    <td class="p-2">{{ $package->delivered_at ? $package->delivered_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="p-2">{{ $package->collected_at ? $package->collected_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="p-2">
                        <a href="{{ route('packages.show', $package) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('packages.edit', $package) }}" class="text-yellow-500">Edit</a> |
                        <form action="{{ route('packages.destroy', $package) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
