@extends('layouts.admin')

@section('content')
<h2>Postmat #{{ $postmat->name }}</h2>
<ul>
    @foreach ($postmat->stashes as $stash)
        <li>
            Stash Size: {{ $stash->size }} |
            @if ($stash->package)
                Package ID: {{ $stash->package->id }} ({{ $stash->package->name ?? 'No Name' }})
            @else
                No Package
            @endif
        </li>
    @endforeach
</ul>
@endsection