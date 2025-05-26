@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Postmats</h1>
        <a href="{{ route('postmats.create') }}" class="form-submit px-4 py-2">+ Create Postmat</a>
    </div>

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