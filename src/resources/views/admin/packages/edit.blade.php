@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Edit Package</h2>

        @if ($errors->any())
            <div class="text-red-600 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('packages.update', $package) }}" class="space-y-4" novalidate>
            @csrf
            @method('PUT')

            <label class="">Sender</label>
            <select name="sender_id" class="form-input">
                <option value="">-- Select sender --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('sender_id', $package->sender_id) == $user->id)>
                        {{ $user->id }}
                    </option>
                @endforeach
            </select>
            @error('sender_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Receiver</label>
            <select name="receiver_id" class="form-input">
                <option value="">-- Select receiver --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('receiver_id', $package->receiver_id) == $user->id)>
                        {{ $user->id }}
                    </option>
                @endforeach
            </select>
            @error('receiver_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Initial Postmat</label>
            <select name="start_postmat_id" class="form-input">
                <option value="">-- Select postmat --</option>
                @foreach ($postmats as $postmat)
                    <option value="{{ $postmat->id }}" @selected(old('start_postmat_id', $package->start_postmat_id) == $postmat->id)>
                        {{ $postmat->name }}
                    </option>
                @endforeach
            </select>
            @error('start_postmat_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Destination Postmat</label>
            <select name="destination_postmat_id" class="form-input">
                <option value="">-- Select postmat --</option>
                @foreach ($postmats as $postmat)
                    <option value="{{ $postmat->id }}" @selected(old('destination_postmat_id', $package->destination_postmat_id) == $postmat->id)>
                        {{ $postmat->name }}
                    </option>
                @endforeach
            </select>
            @error('destination_postmat_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">E-mail</label>
            <input type="email" class="form-input" name="receiver_email"
                value="{{ old('receiver_email', $package->receiver_email) }}" required>
            @error('receiver_email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Receiver phone</label>
            <input type="text" class="form-input" name="receiver_phone"
                value="{{ old('receiver_phone', $package->receiver_phone) }}" required>
            @error('receiver_phone')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <div>
                <label>Unlock Code</label>
                <input type="text" name="unlock_code" placeholder="6-digit code" class="form-input" maxlength="6"
                    pattern="\d{6}" value="{{ old('unlock_code', $package->unlock_code) }}">
                @error('unlock_code')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <label class="">Status</label>
            <select name="status" class="form-input" required>
                @foreach (['registered', 'in_transit', 'in_postmat', 'collected'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $package->status) === $status)>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Size</label>
            <select name="size" class="form-input" required>
                <option value="">-- Select size --</option>
                @foreach (\App\Enums\PackageSize::cases() as $size)
                    <option value="{{ $size->value }}" @if (old('size', $package->size?->value) === $size->value) selected @endif>
                        {{ $size->label() }}
                    </option>
                @endforeach
            </select>
            @error('size')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label class="">Weight (grams)</label>
            <input type="number" class="form-input" name="weight" value="{{ old('weight', $package->weight) }}"
                required>
            @error('weight')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="form-submit border-green-700 bg-green-200">Update</button>
                <a href="{{ route('packages.index') }}" class="form-submit">Cancel</a>
            </div>
        </form>
    </div>
@endsection
