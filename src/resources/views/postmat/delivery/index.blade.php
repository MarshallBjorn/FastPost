@extends('layouts.public')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Postmat Routes</h1>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 border border-green-300">
                {{ session('status') }}
            </div>
        @endif

        <p>Under construction</p>

    </div>
@endsection
