@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Users</h1>
        <a href="{{ route('users.create') }}" class="form-submit px-4 py-2">+ Create User</a>
    </div>

    <div class="overflow-x-auto custom-white-shadow">
        <table class="min-w-full bg-white border-2 border-dotted">
            <thead class="bg-gray-100 text-left text-sm border-2 border-dotted">
                <tr class="text-center">
                    <th class="py-2 px-4">ID</th>
                    <th class="py-2 px-4">First Name</th>
                    <th class="py-2 px-4">Last Name</th>
                    <th class="py-2 px-4">Email</th>
                    <th class="py-2 px-4">Phone</th>
                    <th class="py-2 px-4">Verified</th>
                    <th class="py-2 px-4">Staff Role</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="border-t text-center">
                    <td class="py-2 px-4">{{ $user->id }}</td>
                    <td class="py-2 px-4">{{ $user->first_name }}</td>
                    <td class="py-2 px-4">{{ $user->last_name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">{{ $user->phone ?? '-' }}</td>
                    <td class="py-2 px-4">
                        {{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d') : 'No' }}
                    </td>
                    <td class="py-2 px-4">
                        {{ $user->staff?->staff_type ?? '-' }}
                    </td>
                    <td class="py-2 px-4">
                        <a href="{{ route('users.show', $user) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-500">Edit</a> |
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
