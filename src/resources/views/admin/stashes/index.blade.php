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

        @if ($stashes->isEmpty())
            <p>No stashes found.</p>
        @else
            <div class="overflow-x-auto custom-white-shadow">
                <table class="min-w-full bg-white border-2 border-dotted">
                    <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                        <tr class="text-center">
                            <th class="p-2">ID</th>
                            <th class="p-2">Size</th>
                            <th class="p-2">Package</th>
                            <th class="p-2">Postmat ID</th>
                            <th class="p-2">Reserved Until</th>
                            <th class="p-2">Is Package In</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stashes as $stash)
                            <tr class="border-t text-center">
                                <td class="p-2">{{ $stash->id }}</td>
                                <td class="p-2">{{ $stash->size }}</td>
                                <td class="p-2">
                                    @if (is_null($stash->package))
                                        Empty
                                    @else
                                        <a href="{{ route('packages.show', $stash->package) }}">
                                            {{ $stash->package->id }}
                                        </a>
                                    @endif
                                </td>
                                <td class="p-2">{{ $stash->postmat_id }}</td>
                                <td class="p-2">
                                    {{ $stash->reserved_until ? $stash->reserved_until->format('Y-m-d H:i') : 'Not Reserved' }}
                                </td>
                                <td class="p-2">{{ $stash->is_package_in ? 'Yes' : 'No' }}</td>
                                <td class="p-2 text-amber-400">
                                    <a href="{{ route('stashes.edit', ['stash' => $stash->id]) }}">Edit</a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>
@endsection
