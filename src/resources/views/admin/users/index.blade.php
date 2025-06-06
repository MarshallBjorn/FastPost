@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Users</h1>
        <a href="{{ route('users.create') }}" class="form-submit px-4 py-2">+ Create User</a>
    </div>
    
    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="id" value="{{ request('id') }}" placeholder="User ID"
                class="form-input w-full border border-gray-300 rounded p-2" />

            <div>
                <input type="text" name="firstname" value="{{ request('firstname') }}" placeholder="First Name"
                    class="form-input w-full border border-gray-300 rounded p-2" />

                <input type="text" name="lastname" value="{{ request('lastname') }}" placeholder="Last Name"
                    class="form-input w-full border border-gray-300 rounded p-2" />
            </div>

            <input type="text" name="email" value="{{ request('email') }}" placeholder="Email"
                class="form-input w-full border border-gray-300 rounded p-2" />

            <input type="text" name="staff_type" value="{{ request('staff_type') }}" placeholder="Staff Type"
                class="form-input w-full border border-gray-300 rounded p-2" />
        </div>

        <div class="mt-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('users.index') }}" class="ml-2 text-gray-600">Reset</a>
        </div>
    </form>

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
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
