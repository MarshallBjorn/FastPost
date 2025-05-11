@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Postmat</h2>

    <form method="POST" action="{{ route('postmats.update', $postmat) }}">
        @csrf
        @method('PUT')

        <label class="">Name</label>
        <input type="text" class="form-input" name="name" value="{{ old('name', $postmat->name) }}" required>

        <label class="">City</label>
        <input type="text" class="form-input" name="city" value="{{ old('city', $postmat->city) }}" required>

        <label class="">Post Code</label>
        <input type="text" class="form-input" name="post_code" value="{{ old('post_code', $postmat->post_code) }}" required>

        <label class="">Latitude</label>
        <input type="text" class="form-input" name="latitude" value="{{ old('latitude', $postmat->latitude) }}" required>

        <label class="">Longitude</label>
        <input type="text" class="form-input" name="longitude" value="{{ old('longitude', $postmat->longitude) }}" required>

        <label class="">Status</label>
        <select name="status" class="form-input" required>
            @foreach (['active', 'unavailable', 'maintenance'] as $status)
                <option value="{{ $status }}" @selected($postmat->status === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <div class="flex items-center justify-between mt-4">
            <button type="submit" class="form-submit border-green-700 bg-green-200">Update</button>
            <a href="{{ route('postmats.index') }}" class="form-submit">Cancel</a>
        </div>
    </form>
</div>
@endsection
