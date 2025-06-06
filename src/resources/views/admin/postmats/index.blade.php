@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Postmats</h1>
        <a href="{{ route('postmats.create') }}" class="form-submit px-4 py-2">+ Create Postmat</a>
    </div>

    <form method="GET" action="{{ route('postmats.index') }}" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="id" value="{{ request('id') }}" placeholder="ID"
                class="form-input w-full border border-gray-300 rounded p-2" />

            <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                class="form-input w-full border border-gray-300 rounded p-2" />

            <input type="text" name="city" value="{{ request('city') }}" placeholder="City"
                class="form-input w-full border border-gray-300 rounded p-2" />

            <select name="status" class="form-select w-full border border-gray-300 rounded p-2">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>

        <div class="mt-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('postmats.index') }}" class="ml-2 text-gray-600">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="p-2">Name</th>
                    <th class="p-2">City</th>
                    <th class="p-2">Post-Code</th>
                    <th class="p-2">Latitude</th>
                    <th class="p-2">Longitude</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($postmats as $postmat)
                <tr class="border-t text-center">
                    <td class="p-2">{{ $postmat->name }}</td>
                    <td class="p-2">{{ $postmat->city }}</td>
                    <td class="p-2">{{ $postmat->post_code }}</td>
                    <td class="p-2">{{ $postmat->latitude }}</td>
                    <td class="p-2">{{ $postmat->longitude }}</td>
                    <td class="p-2">{{ ucfirst($postmat->status) }}</td>
                    <td class="p-2">
                        <a href="{{ route('postmats.show', $postmat) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('postmats.edit', $postmat) }}" class="text-yellow-500">Edit</a> |
                        <form action="{{ route('postmats.destroy', $postmat) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="mt-6">
            {{ $postmats->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection