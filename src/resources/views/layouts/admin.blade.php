<!DOCTYPE html>
<html>
<head>
    <title>FastPost Admin Panel</title>    
    {{-- WARN: NOT FOR PRODUCTION USE --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- ***** --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="font-family: 'Courier Prime', monospace;">
    <header class="flex justify-between mb-2 border-dotted border-b-2">
        <a href="{{ route('admin.dashboard') }}" class="p-3 text-2xl border-dotted border-r-2">Admin Panel</a>
        <div class="my-auto px-5 flex gap-x-6">
            <a href="{{ route('packages.index') }}">Deliveries</a>
            <a href="{{ route('postmats.index') }}">Postmats</a>
            <a href="{{ route('actualizations.index')}}">Actualizations</a>
            <a href="">Accounts</a>
        </div>        
    </header>

    <main class="w-11/12 mx-auto">
        @yield('content')
    </main>
</body>
</html>
