@extends('layouts.public')

@section('content')
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-8">My Current Packages</h1>

        @if (session('errors') && is_array(session('errors')))
            <div class="mb-4 p-4 border border-red-400 bg-red-50 rounded text-red-700">
                <strong>Some errors occurred:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 border border-green-400 bg-green-50 rounded text-green-700">
                {{ session('success') }}
            </div>
        @endif


        @php
            // Divide packages into two groups based on route remaining
            $toWarehousePackages = $packages->filter(function ($package) {
                $route = json_decode(
                    optional($package->latestActualization)->route_remaining ?? $package->route_path,
                    true,
                );
                return !empty($route);
            });

            $toPostmatPackages = $packages->filter(function ($package) {
                $route = json_decode(
                    optional($package->latestActualization)->route_remaining ?? $package->route_path,
                    true,
                );
                return empty($route);
            });
        @endphp

        @if ($toWarehousePackages->isNotEmpty())
            <form action="{{ route('postmat.delivery.putPackagesInWarehouse') }}" method="POST" class="mb-8">
                @csrf

                <div class="mb-4 flex items-center space-x-3">
                    <input type="checkbox" id="checkAll" class="form-checkbox h-5 w-5 text-blue-600 cursor-pointer">
                    <label for="checkAll" class="select-none text-lg font-medium text-gray-700 cursor-pointer">Check All
                        Packages to Put in Warehouse</label>
                </div>

                <div class="max-h-72 overflow-y-auto border rounded-md shadow-sm p-4 bg-white">
                    @foreach ($toWarehousePackages as $package)
                        <label class="flex items-center space-x-3 p-2 rounded cursor-pointer hover:bg-blue-50 transition"
                            for="package_{{ $package->id }}">
                            <input type="checkbox" name="package_ids[]" value="{{ $package->id }}"
                                id="package_{{ $package->id }}" class="form-checkbox h-5 w-5 text-blue-600">
                            <span class="text-gray-800 font-medium">#{{ $package->id }}</span>
                            <span class="text-sm text-gray-500">Status: {{ ucfirst($package->status->value) }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit"
                    class="mt-6 w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-200">
                    Put Packages in Warehouse
                </button>
            </form>
        @endif

        @if ($toPostmatPackages->isNotEmpty())
            <form action="{{ route('postmat.delivery.deliverToPostmat') }}" method="POST" class="mb-8">
                @csrf

                <h2 class="text-2xl font-semibold mb-4">📤 Packages to Deliver to Postmat</h2>

                <div class="mb-4 flex items-center space-x-3">
                    <input type="checkbox" id="checkAllDeliveries" class="form-checkbox h-5 w-5 text-blue-600 cursor-pointer">
                    <label for="checkAllDeliveries" class="select-none text-lg font-medium text-gray-700 cursor-pointer">
                        Check All Packages to Deliver
                    </label>
                </div>

                <div class="max-h-72 overflow-y-auto border rounded-md shadow-sm p-4 bg-white">
                    @foreach ($toPostmatPackages as $package)
                        <label class="flex items-center space-x-3 p-2 rounded cursor-pointer hover:bg-blue-50 transition"
                               for="deliver_package_{{ $package->id }}">
                            <input type="checkbox" name="package_ids[]" value="{{ $package->id }}"
                                   id="deliver_package_{{ $package->id }}" class="form-checkbox h-5 w-5 text-blue-600">
                            <span class="text-gray-800 font-medium">#{{ $package->id }}</span>
                            <span class="text-sm text-gray-500">
                                To: {{ optional($package->destinationPostmat)->name ?? '-' }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <button type="submit"
                        class="mt-6 w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-200">
                    Deliver Packages to Postmat
                </button>
            </form>
        @endif

        @if ($packages->isEmpty())
            <p class="text-gray-500 text-center mt-12 text-lg">You currently have no packages assigned.</p>
        @endif
    </div>

    <script>
        document.getElementById('checkAll')?.addEventListener('change', function(e) {
            const checked = e.target.checked;
            document.querySelectorAll('input[name="package_ids[]"]').forEach(checkbox => {
                checkbox.checked = checked;
            });
        });

        document.getElementById('checkAllDeliveries')?.addEventListener('change', function(e) {
            const checked = e.target.checked;
            document.querySelectorAll('#deliver_package_' + e.target.id).forEach(checkbox => {
                checkbox.checked = checked;
            });

            document.querySelectorAll('input[name="package_ids[]"]').forEach(cb => {
                if (cb.id.startsWith('deliver_package_')) cb.checked = checked;
            });
        });
    </script>
@endsection
