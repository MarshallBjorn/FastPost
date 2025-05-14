@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Package</h2>

    <form method="POST" action="{{ route('packages.update', $package) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <label class="">Sender</label>
        <select name="sender_id" class="form-input">
            <option value="">-- Select sender --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected($package->sender_id == $user->id)>{{ $user->id }}</option>
            @endforeach
        </select>

        <label class="">Receiver</label>
        <select name="receiver_id" class="form-input">
            <option value="">-- Select receiver --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected($package->receiver_id == $user->id)>{{ $user->id }}</option>
            @endforeach
        </select>

        <label class="">Initial Postmat</label>
        <select name="start_postmat_id" class="form-input">
            <option value="">-- Select postmat --</option>
            @foreach ($postmats as $postmat)
                <option value="{{ $postmat->id }}" @selected($package->start_postmat_id == $postmat->id)>{{ $postmat->name }}</option>
            @endforeach
        </select>

        <label class="">Destination Postmat</label>
        <select name="destination_postmat_id" class="form-input">
            <option value="">-- Select postmat --</option>
            @foreach ($postmats as $postmat)
                <option value="{{ $postmat->id }}" @selected($package->destination_postmat_id == $postmat->id)>{{ $postmat->name }}</option>
            @endforeach
        </select>

        <label class="">E-mail</label>
        <input type="email" class="form-input" name="receiver_email" value="{{ old('receiver_email', $package->receiver_email) }}" required>

        <label class="">Receiver phone</label>
        <input type="text" class="form-input" name="receiver_phone" value="{{ old('receiver_phone', $package->receiver_phone) }}" required>


        <label class="">Status</label>
        <select name="status" class="form-input" required>
            @foreach (['registered', 'in_transit', 'in_postmat', 'collected'] as $status)
                <option value="{{ $status }}" @selected($package->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>

        <div class="flex items-center justify-between mt-4">
            <button type="submit" class="form-submit border-green-700 bg-green-200">Update</button>
            <a href="{{ route('packages.index') }}" class="form-submit">Cancel</a>
        </div>
    </form>
</div>
@endsection
