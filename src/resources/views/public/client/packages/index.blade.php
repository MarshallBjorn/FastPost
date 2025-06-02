@extends('layouts.public')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Sent Packages</h1>

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif


        @if ($packages->isEmpty())
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg">
                You have not sent any packages yet.
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full table-auto text-sm text-left text-gray-600">
                    <thead class="bg-blue-500 text-xs font-semibold uppercase tracking-wider text-white">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Size</th>
                            <th class="px-6 py-4">Weight (g)</th>
                            <th class="px-6 py-4">To Postmat</th>
                            <th class="px-6 py-4">Created</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($packages as $package)
                            <tr>
                                <td class="px-6 py-4">{{ $package->id }}</td>
                                <td class="px-6 py-4 capitalize">{{ $package->status }}</td>
                                <td class="px-6 py-4">{{ $package->size }}</td>
                                <td class="px-6 py-4">{{ $package->weight }}</td>
                                <td class="px-6 py-4">
                                    {{ optional($package->destinationPostmat)->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">{{ $package->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    @if ($package->status->value === 'registered')
                                        {{-- POST: put_package_in_postmat --}}
                                        <form action="{{ route('client.put_package_in_postmat') }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                                            <button type="submit" class="text-green-600 hover:text-green-800 font-medium">
                                                Open Stash
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('package.lookup') . '?code=' . $package->id }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            Track
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
