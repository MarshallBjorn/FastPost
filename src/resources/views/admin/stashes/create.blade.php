@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Stash</h1>

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

    <form action="{{ route('stashes.store', $postmat) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <!-- Size -->
            <label class="block mb-2">Size</label>
            <input type="text" class="form-input mb-4" name="size" value="{{ old('size') }}" required>

            <!-- Package ID -->
            <label class="block mb-2">Package ID (optional)</label>
            <input type="text" class="form-input mb-4" name="package_id" value="{{ old('package_id') }}">

            <!-- Reserved Until -->
            <label class="block mb-2">Reserved Until (YYYY-MM-DD HH:MM:SS)</label>
            <input type="datetime-local" class="form-input mb-4" name="reserved_until" value="{{ old('reserved_until') }}">

            <!-- Is Package In -->
            <label class="block mb-2">Is Package In</label>
            <input type="hidden" name="is_package_in" value="0">
            <input type="checkbox" name="is_package_in" value="1" class="form-checkbox mb-4" @checked(old('is_package_in'))>
        </div>

        <button class="form-submit">Create</button>
@endsection