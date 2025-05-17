@extends('layouts.admin')

@section('content')
    <h2>
        Postmat #{{ $postmat->name }}</h2>
    <ul>
        @foreach ($postmat->stashes as $stash)
            <li>
                <strong>Stash Size:</strong> {{ $stash->size }}<br>
                <strong>Postmat ID:</strong> {{ $stash->postmat_id }}<br>
                <strong>Package:</strong>
                @if ($stash->package)
                    ID: {{ $stash->package->id }} ({{ $stash->package->name ?? 'No Name' }})
                @else
                    No Package
                @endif
                <br>
                <strong>Reserved Until:</strong> {{ $stash->reserved_until ?? 'Not Reserved' }}<br>
                <strong>Is Package In:</strong> {{ $stash->is_package_in ? 'Yes' : 'No' }}
            </li>
        @endforeach
    </ul>
@endsection
