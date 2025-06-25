@extends('layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Create Package</h1>

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

        <form action="{{ route('packages.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label>Sender</label>
                <select name="sender_id" class="form-input">
                    <option value="">-- Select Sender --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('sender_id') == $user->id)>
                            {{ $user->id }} - {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Receiver</label>
                <select name="receiver_id" class="form-input">
                    <option value="">-- Select Receiver --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('receiver_id') == $user->id)>
                            {{ $user->id }} - {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Initial Postmat</label>
                <select name="start_postmat_id" class="form-input">
                    <option value="">-- Select Initial Postmat --</option>
                    @foreach ($postmats as $postmat)
                        <option value="{{ $postmat->id }}" @selected(old('start_postmat_id') == $postmat->id)>
                            {{ $postmat->name }} ({{ $postmat->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Destination Postmat</label>
                <select name="destination_postmat_id" class="form-input">
                    <option value="">-- Select Destination Postmat --</option>
                    @foreach ($postmats as $postmat)
                        <option value="{{ $postmat->id }}" @selected(old('destination_postmat_id') == $postmat->id)>
                            {{ $postmat->name }} ({{ $postmat->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="size">Size</label>
                <select name="size" id="size" class="form-input" required>
                    <option value="">Select Size</option>
                    <option value="S" @selected(old('size') == 'S')>S</option>
                    <option value="M" @selected(old('size') == 'M')>M</option>
                    <option value="L" @selected(old('size') == 'L')>L</option>
                </select>
            </div>

            <div>
                <label>Package size in grams</label>
                <input type="number" name="weight" placeholder="Weight in grams" class="form-input"
                    value="{{ old('weight') }}" required>
            </div>

            <div>
                <label>Receiver phone</label>
                <input type="text" name="receiver_phone" placeholder="Receiver Phone" class="form-input"
                    value="{{ old('receiver_phone') }}" required>
            </div>
            <div>
                <label>Receiver email</label>
                <input type="text" name="receiver_email" placeholder="Receiver Email" class="form-input"
                    value="{{ old('receiver_email') }}" required>
            </div>
            <div>
                <label>Unlock Code</label>
                <input type="text" name="unlock_code" placeholder="6-digit code" class="form-input" maxlength="6"
                    pattern="\d{6}" value="{{ old('unlock_code') }}">
            </div>

            <div>
                <label for="status">Status</label>
                <select name="status" id="status" class="form-input" required>
                    <option value="">Select Status</option>
                    <option value="registered" @selected(old('status') == 'registered')>Registered</option>
                    <option value="in_transit" @selected(old('status') == 'in_transit')>In Transit</option>
                    <option value="in_postmat" @selected(old('status') == 'in_postmat')>In Postmat</option>
                    <option value="collected" @selected(old('status') == 'collected')>Collected</option>
                </select>
            </div>

            <button class="form-submit">Create</button>
        </form>
    </div>
@endsection