@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 border-2 border-dotted mt-12">
    <h1 class="text-3xl font-bold mb-6">Actualization Details</h1>

    <div class="grid grid-cols-2 gap-x-4 max-w-xl">
        <div class="contents">
            <div class="font-semibold px-4 py-2">Package ID:</div>
            <div class="px-4 py-2">{{ $actualization->package_id }}</div>
        </div>

        <div class="contents">
            <div class="font-semibold px-4 py-2">Message:</div>
            <div class="px-4 py-2">{{ $actualization->message }}</div>
        </div>

        <div class="contents">
            <div class="font-semibold px-4 py-2">Last Courier:</div>
            <div class="px-4 py-2">{{ $actualization->courier?->id ?? 'N/A' }}</div>
        </div>

        <div class="contents">
            <div class="font-semibold px-4 py-2">Current Warehouse:</div>
            {{ $actualization->currentWarehouse?->name ?? 'N/A' }}
        </div>

        <div class="contents">
            <div class="font-semibold px-4 py-2">Created At:</div>
            <div class="px-4 py-2">{{ $actualization->created_at }}</div>
        </div>
    </div>

    <div class="mt-6 space-x-4">
        <a href="{{ route('actualizations.edit', $actualization) }}" class="text-yellow-500">Edit</a>

        <form action="{{ route('actualizations.destroy', $actualization) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Are you sure?')" class="text-red-500">Delete</button>
        </form>
    </div>
</div>
@endsection
