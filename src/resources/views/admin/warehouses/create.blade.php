@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Warehouse</h1>

    @if ($errors->any())
        <h1 class="mt-2 text-xl text-center text-red-600">Errors!</h1>
        <div class="text-black p-4 mb-4 border-dotted border-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('warehouses.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label>City</label>
            <input type="text" name="city" placeholder="City" class="form-input" required>
        </div>

        <div>
            <label>Post Code</label>
            <input type="text" name="post_code" placeholder="Post Code" class="form-input" required>
        </div>

        <div>
            <label>Latitude</label>
            <input type="text" name="latitude" placeholder="Latitude" class="form-input" required>
        </div>

        <div>
            <label>Longitude</label>
            <input type="text" name="longitude" placeholder="Longitude" class="form-input" required>
        </div>

        <div>
            <label>Status</label>
            <select name="status" class="form-input" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="unavailable">Unavailable</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <button class="form-submit">Create</button>
    </form>
</div>
@endsection