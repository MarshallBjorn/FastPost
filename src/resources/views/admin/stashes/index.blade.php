@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 border-2 border-dotted mt-12">
    <h1 class="text-3xl font-bold mb-4">Postmat Details</h1>

    <div class="mb-6">
        <div class="grid grid-cols-2 max-w-xl">
            <div class="font-semibold">ID:</div>
            <div>{{ $postmat->id }}</div>
            
            <div class="font-semibold">Name:</div>
            <div>{{ $postmat->name }}</div>
        
            <div class="font-semibold">City:</div>
            <div>{{ $postmat->city }}</div>

            <div class="font-semibold">Post-Code:</div>
            <div>{{ $postmat->post_code }}</div>

            <div class="font-semibold">Latitude:</div>
            <div>{{ $postmat->latitude }}</div>

            <div class="font-semibold">Longitude:</div>
            <div>{{ $postmat->longitude }}</div>

            <div class="font-semibold">Status:</div>
            <div>{{ $postmat->status }}</div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold mb-4">Stashes</h1>
        <a href="{{ route('stashes.create', $postmat) }}" class="form-submit px-4 py-2">+ Create Stashes</a>
    </div>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="p-2">ID</th>
                    <th class="p-2">Size</th>
                    <th class="p-2">Package</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stashes as $stash)
                <tr class="border-t text-center">
                    <td class="p-2">{{ $stash->id }}</td>
                    <td class="p-2">{{ $stash->size }}</td>
                    @if (is_null($stash->package_id))
                        <td class="p-2">Empty</td>
                    @else
                        <td class="p-2">
                            <a href="{{ route('admin.package.show', $stash->package) }}">
                                $stash->package->package_id
                            </a>
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    

    @if ($stashes->isEmpty())
        <p>No stashes found.</p>
    @else
        <ul>
            @foreach ($stashes as $stash)
                <li>{{ $stash->id }} — {{ $stash->created_at }}</li>
            @endforeach
        </ul>
    @endif
@endsection
