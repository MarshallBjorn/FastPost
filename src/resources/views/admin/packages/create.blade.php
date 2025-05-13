@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Package</h1>

    @if ($errors->any())
        <h1 class="mt-2 text-xl text-center">Errors!</h1>
        <div class= "text-black p-4 mb-4 border-dotted border-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('packages.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label>Sender</label>
            <select name="sender_id" class="form-input">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->id }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Receiver</label>
            <select name="receiver_id" class="form-input">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->id }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Initial Postmat</label>
            <select name="start_postmat_id" class="form-input">
                @foreach ($postmats as $postmat)
                    <option value="{{ $postmat->id }}">{{ $postmat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Destination Postmat</label>
            <select name="destination_postmat_id" class="form-input">
                @foreach ($postmats as $postmat)
                    <option value="{{ $postmat->id }}">{{ $postmat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Receiver phone</label>
            <input type="text" name="receiver_phone" placeholder="Receiver Phone" class="form-input" required>
        </div>
        <div>
            <label>Receiver email</label>
            <input type="text" name="receiver_email" placeholder="Receiver Email" class="form-input" required>
        </div>
        <div>
            <label for="status">Status</label>
            <select name="status" id="status" class="form-input" required>
                <option value="">Select Status</option>
                <option value="registered">Registered</option>
                <option value="in_transit">In Transit</option>
                <option value="in_postmat">In Postmat</option>
                <option value="collected">Collected</option>
            </select>
        </div>

        <button class="form-submit">Create</button>
    </form>
</div>
@endsection
