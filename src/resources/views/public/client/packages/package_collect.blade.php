@extends('layouts.public')

@section('content')
<div class="max-w-md mx-auto mt-20 px-6 py-8 bg-white shadow-xl rounded-xl">
    <h2 class="text-2xl font-bold text-center mb-6">Collect Your Package</h2>

    @if (session('status'))
        <div class="mb-4 p-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('client.collect_package.submit') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Receiver Phone</label>
            <input
                type="text"
                name="receiver_phone"
                value="{{ old('receiver_phone') }}"
                placeholder="Enter phone number"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required>
            @error('receiver_phone')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unlock Code</label>
            <input
                type="text"
                name="unlock_code"
                value="{{ old('unlock_code') }}"
                placeholder="6-digit code"
                maxlength="6"
                pattern="\d{6}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required>
            @error('unlock_code')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
            Open Stash
        </button>
    </form>
</div>
@endsection
