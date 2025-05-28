@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create User</h1>

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

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label>First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-input" required>
        </div>

        <div>
            <label>Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-input" required>
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
        </div>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" required>
        </div>

        <div>
            <label>Password</label>
            <input type="password" name="password" class="form-input" required>
        </div>

        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-input" required>
        </div>

        <hr class="my-6">

        <div>
            <label>Staff Type</label>
            <select name="staff[staff_type]" class="form-input">
                <option value="">-- None --</option>
                @foreach(['admin', 'postmat_courier', 'warehouse_courier', 'warehouse'] as $type)
                    <option value="{{ $type }}" {{ old('staff.staff_type') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Hire Date</label>
            <input type="datetime-local" name="staff[hire_date]" class="form-input" value="{{ old('staff.hire_date') }}">
        </div>

        <label for="warehouse">Warehouse</label>
        <select name="staff[warehouse_id]" id="warehouse" class="form-input">
            <option value="">Select Warehouse</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}"
                    {{ old('staff.warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->city }}
                </option>
            @endforeach
        </select>


        <div class="flex items-center justify-between mt-4">
            <button class="form-submit">Create</button>
            <a href="{{ route('users.index') }}" class="form-submit">Cancel</a>
        </div>
    </form>
</div>
@endsection
