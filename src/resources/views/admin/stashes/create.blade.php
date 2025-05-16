@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Stash</h1>

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

    <form action="{{ route('stashes.store', $postmat) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label>Size</label>
            <select name="size" class="form-input" required>
                <option value="">Select Size</option>
                <option value="S">Small</option>
                <option value="M">Medium</option>
                <option value="L">Large</option>
            </select>
        </div>

        <button class="form-submit">Create</button>
@endsection