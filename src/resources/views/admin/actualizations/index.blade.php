@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Actualizations</h1>
    </div>

    <form method="GET" action="{{ route('actualizations.index') }}" class="mb-4">
        <div class="form-group">
            <label for="id">Filter by ID</label>
            <input type="text" name="id" id="id" value="{{ request('id') }}" class="form-control" placeholder="Enter ID">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form> 

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="p-2 px-4">Package ID</th>
                    <th class="p-2 px-4">Message</th>
                    <th class="p-2 px-4">Created At</th>
                    <th class="p-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actualizations as $a)
                    <tr class="border-b text-center">
                        <td class="p-2 px-4">{{ $a->package_id }}</td>
                        <td class="p-2 px-4">{{ $a->message }}</td>
                        <td class="p-2 px-4">{{ $a->created_at }}</td>
                        <td class="p-2 px-4">
                            <a href="{{ route('actualizations.show', $a) }}" class="text-blue-500">View</a> |
                            <a href="{{ route('actualizations.edit', $a) }}" class="text-yellow-500">Edit</a> |
                            <form action="{{ route('actualizations.destroy', $a) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete?')" class="text-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $actualizations->links() }}
    </div>
</div>
@endsection
