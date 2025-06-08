@extends('layouts.public')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Edit Your Profile</h2>

    @if (session('status'))
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        <input type="hidden" name="_profile_update" value="1">

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full border rounded px-3 py-2" required>
            @error('first_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full border rounded px-3 py-2" required>
            @error('last_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border rounded px-3 py-2">
            @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <hr class="my-4">

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">New Password (optional)</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
            @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update Profile</button>
    </form>
</div>
@endsection
