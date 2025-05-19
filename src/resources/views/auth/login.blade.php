@extends('layouts.public')

@section('content')

@if (session('auth_required'))
    <div class="max-w-5xl mx-auto bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4 border border-yellow-300">
        {{ session('auth_required') }}
    </div>
@endif

<div class="max-w-5xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-2 gap-10 bg-white p-6 rounded shadow">

    {{-- Login Form --}}
    <div>
        <h2 class="text-xl font-bold mb-4">Login</h2>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="form_type" value="login">
            @if(session('login_error'))
                <p class="text-red-500">{{ session('login_error') }}</p>
            @endif
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="login_email" value="{{ old('login_email') }}" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Login</button>
        </form>
    </div>

    {{-- Register Form --}}
    <div>
        <h2 class="text-xl font-bold mb-4">Register</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <input type="hidden" name="form_type" value="register">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"  class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password"  class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Register</button>
        </form>
    </div>
</div>
@endsection
