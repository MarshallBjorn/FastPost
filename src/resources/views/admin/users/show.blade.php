@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">User Details</h1>

    <div class="border p-4 space-y-4">
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>First Name:</strong> {{ $user->first_name }}</p>
        <p><strong>Last Name:</strong> {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone ?? '-' }}</p>
        <p><strong>Email Verified At:</strong> {{ $user->email_verified_at ?? 'Not verified' }}</p>

        @if ($user->staff)
            <hr>
            <p><strong>Staff Role:</strong> {{ $user->staff->staff_type }}</p>
            <p><strong>Hire Date:</strong> {{ $user->staff->hire_date }}</p>
            <p><strong>Warehouse:</strong> {{ $user->staff->warehouse->city ?? '-' }}</p>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('users.edit', $user) }}" class="text-yellow-500">Edit</a> |
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="text-red-500" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </div>
</div>
@endsection
