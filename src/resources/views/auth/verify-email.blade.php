@extends('layouts.public')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Verify Your Email</h2>
    <p class="mb-4">A verification link has been sent to your email address.</p>

    @if (session('message'))
        <div class="text-green-600 mb-4">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">
            Resend Verification Email
        </button>
    </form>
</div>
@endsection
