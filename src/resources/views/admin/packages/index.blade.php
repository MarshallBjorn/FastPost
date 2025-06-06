@php
    $queryParams = request()->query();
@endphp

@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Packages</h1>
        <a href="{{ route('packages.create') }}" class="form-submit px-8 py-2">+ Create Package</a>
    </div>

    <form method="GET" action="{{ route('packages.index') }}" class="mb-4 space-y-2">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            <input type="text" name="id" value="{{ request('id') }}" placeholder="Package ID" class="border p-2 rounded" />

            <input type="text" name="sender_email" value="{{ request('sender_email') }}" placeholder="Sender Email" class="border p-2 rounded" />

            <input type="text" name="receiver_email" value="{{ request('receiver_email') }}" placeholder="Receiver Email" class="border p-2 rounded" />

            <input type="text" name="receiver_phone" value="{{ request('receiver_phone') }}" placeholder="Receiver Phone" class="border p-2 rounded" />

            <select name="status" class="border p-2 rounded">
                <option value="">Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') == $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>

            <select name="size" class="border p-2 rounded">
                <option value="">Size</option>
                @foreach ($sizes as $size)
                    <option value="{{ $size->value }}" @selected(request('size') == $size->value)>
                        {{ $size->value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2 mt-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('packages.index') }}" class="text-sm text-gray-600 underline">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="py-2 px-8">ID</th>
                    <th class="py-2 px-8">Sender</th>
                    <th class="py-2 px-8">Receiver</th>
                    <th class="py-2 px-8">Status</th>
                    <th class="py-2 px-8">Initial</th>
                    <th class="py-2 px-8">Destination</th>
                    <th class="py-2 px-8">Receiver_email</th>
                    <th class="py-2 px-8">Receiver_phone</th>
                    <th class="py-2 px-8">Unlock code</th>
                    <th class="py-2 px-8">Sent at</th>
                    <th class="py-2 px-8">Weight</th>
                    <th class="py-2 px-8">Size</th>
                    <th class="py-2 px-8">Delivered date</th>
                    <th class="py-2 px-8">Collected date</th>
                    <th class="py-2 px-8">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                <tr class="border-t text-center">
                    <td class="py-2 px-8">{{ $package->id }}</td>
                    <td class="py-2 px-8">{{ $package->sender->name ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->receiver->name ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->status->label() }}</td>
                    <td>{{ $package->startPostmat->name ?? '-' }}</td>
                    <td>{{ $package->destinationPostmat->name ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->receiver_email }}</td>
                    <td class="py-2 px-8">{{ $package->receiver_phone }}</td>
                    <td class="py-2 px-8">{{ $package->unlock_code }}</td>
                    <td class="py-2 px-8">{{ $package->sent_at ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->weight ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->size ?? '-' }}</td>
                    <td class="py-2 px-8">{{ $package->delivered_at ? $package->delivered_at : '-' }}</td>
                    <td class="py-2 px-8">{{ $package->collected_at ? $package->collected_at : '-' }}</td>
                    <td class="py-2 px-8">
                        <form action="{{ route('packages.advance', $package) . '?' . http_build_query($queryParams) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-cyan-500">Advance</button>
                        </form>
                        <a href="{{ route('packages.show', $package) . '?' . http_build_query($queryParams) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('packages.edit', $package) . '?' . http_build_query($queryParams) }}" class="text-yellow-500">Edit</a> |
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

    <div class="mt-4">
        {{ $packages->links() }}
    </div>
</div>
@endsection
