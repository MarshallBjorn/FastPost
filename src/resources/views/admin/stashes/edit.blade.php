@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Stash</h2>

    <form method="POST" action="{{ route('stashes.update', $stash) }}">
        @csrf
        @method('PUT')

        <!-- Postmat ID -->
        <label class="block mb-2">Postmat</label>
        <select name="postmat_id" class="form-input mb-4" required>
            @foreach ($postmats as $postmat)
                <option value="{{ $postmat->id }}" @selected(old('postmat_id', $stash->postmat_id) == $postmat->id)>
                    {{ $postmat->name }}
                </option>
            @endforeach
        </select>

        <!-- Size -->
        <label class="block mb-2">Size</label>
        <input type="text" class="form-input mb-4" name="size" value="{{ old('size', $stash->size) }}" required>

        <!-- Package ID -->
        <label class="block mb-2">Package ID (optional)</label>
        <input type="text" class="form-input mb-4" name="package_id" value="{{ old('package_id', $stash->package_id) }}">

        <!-- Reserved Until -->
        <label class="block mb-2">Reserved Until (YYYY-MM-DD HH:MM:SS)</label>
        <input type="datetime-local" class="form-input mb-4" name="reserved_until" value="{{ old('reserved_until', optional($stash->reserved_until)->format('Y-m-d\TH:i')) }}">

        <!-- Is Package In -->
        <label class="block mb-2">Is Package In</label>
        <input type="hidden" name="is_package_in" value="0">
        <input type="checkbox" name="is_package_in" value="1" class="form-checkbox mb-4" @checked(old('is_package_in', $stash->is_package_in))>

        <div class="flex items-center justify-between mt-4">
            <button type="submit" class="form-submit border-green-700 bg-green-200">Update</button>
            <a href="{{ route('stashes.index', $postmat) }}" class="form-submit">Cancel</a>
        </div>
    </form>
</div>
@endsection
