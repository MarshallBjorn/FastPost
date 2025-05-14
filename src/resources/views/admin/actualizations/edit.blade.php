@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Actualization</h1>

    @if ($errors->any())
        <h1 class="mt-2 text-xl text-center">Errors!</h1>
        <div class="text-black p-4 mb-4 border-dotted border-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('actualizations.update', $actualization) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label>Package</label>
            <select name="package_id" class="form-input" required>
                @foreach ($packages as $package)
                    <option value="{{ $package->id }}" {{ $actualization->package_id == $package->id ? 'selected' : '' }}>
                        Package #{{ $package->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="">
            <label class="block mb-1">Message</label>
            <select name="message" class="border p-2 w-full" required>
                <option value="">Select Message</option>
                <option value="sent" {{ old('message', $actualization->message) == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="in_warehouse" {{ old('message', $actualization->message) == 'in_warehouse' ? 'selected' : '' }}>In Warehouse</option>
                <option value="in_delivery" {{ old('message', $actualization->message) == 'in_delivery' ? 'selected' : '' }}>In Delivery</option>
            </select>
        </div>


        <div>
            <label>Last Courier ID</label>
            <input type="number" name="last_courier_id" value="{{ old('last_courier_id', $actualization->last_courier_id) }}" class="form-input" placeholder="Courier ID (optional)">
        </div>

        <div>
            <label>Last Warehouse ID</label>
            <input type="number" name="last_warehouse_id" value="{{ old('last_warehouse_id', $actualization->last_warehouse_id) }}" class="form-input" placeholder="Warehouse ID (optional)">
        </div>

        <div>
            <label>Created At</label>
            <input type="datetime-local" name="created_at" value="{{ old('created_at', \Carbon\Carbon::parse($actualization->created_at)->format('Y-m-d\TH:i')) }}" class="form-input" required>
        </div>

        <button class="form-submit">Update</button>
    </form>
</div>
@endsection
