<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name', 'FastPost') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-semibold text-blue-600 hover:text-blue-700 transition">
                📦 FastPost
            </a>
            <nav class="space-x-4 text-sm font-medium">
                @auth
                    @php
                        $staff = auth()->user()->staff;
                        $staffType = $staff?->staff_type;
                    @endphp

                    @switch($staffType)
                        @case('admin')
                            <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 transition">Admin page</a>
                            @break

                        @case('postmat_courier')
                            <a href="{{ route('postmat.delivery.index') }}" class="text-gray-700 hover:text-blue-600 transition">Courier page</a>
                            @break

                        @case('warehouse_courier')
                            <a href="{{ route('warehouse.delivery.index') }}" class="text-gray-700 hover:text-blue-600 transition">Courier page</a>
                            @break
                    @endswitch
                @endauth
                <a href="{{ route('public.postmats.index') }}" class="text-gray-700 hover:text-blue-600 transition">Browse Postmats</a>
                <a href="{{ route('client.send_package') }}" class="text-gray-700 hover:text-blue-600 transition">Send a parcel</a>
                <a href="{{ route('client.collect_package') }}" class="text-gray-700 hover:text-blue-600 transition">Collect a parcel</a>
                @auth
                    <a href="{{ route('client.packages') }}" class="text-gray-700 hover:text-blue-600 transition">Your parcels</a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 transition">Your profile</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-blue-600 transition">Logout {{auth()->user()->first_name}}</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="text-gray-700 hover:text-blue-600 transition">Login</a>
                @endauth
            </nav>
        </div>
        @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 text-center">
                ⚠️ Please verify your email address.
                <form action="{{ route('verification.send') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="underline">Resend verification email</button>
                </form>
            </div>
        @endif
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            @if (session('auth_required'))
                <div class="max-w-7xl mx-auto bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4 border border-yellow-300">
                    {{ session('auth_required') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-10">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} FastPost - When speed is mandatory
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
