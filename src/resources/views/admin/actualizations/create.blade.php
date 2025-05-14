@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Actualization</h1>

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

    <form action="{{ route('actualizations.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label>Package</label>
            <select name="package_id" class="form-input" required>
                @foreach ($packages as $package)
                    <option value="{{ $package->id }}">#{{ $package->id }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Message</label>
            <select name="message" class="form-input" required>
                <option value="">Select Message</option>
                <option value="sent">Sent</option>
                <option value="in_warehouse">In Warehouse</option>
                <option value="in_delivery">In Delivery</option>
            </select>
        </div>

        <div>
            <label>Last Courier ID</label>
            <input type="number" name="last_courier_id" placeholder="Courier ID (optional)" class="form-input">
        </div>

        <div>
            <label>Last Warehouse ID</label>
            <input type="number" name="last_warehouse_id" placeholder="Warehouse ID (optional)" class="form-input">
        </div>

        <div>
            <label>Created At</label>
            <input type="datetime-local" name="created_at" class="form-input" required>
        </div>

        <button class="form-submit">Create</button>
    </form>
</div>
@endsection
