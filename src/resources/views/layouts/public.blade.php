<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name', 'FastPost') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
                <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 transition">Admin page</a>
                <a href="{{ route('public.postmats.index') }}" class="text-gray-700 hover:text-blue-600 transition">Browse Postmats</a>
                <a href="{{ route('client.send_package') }}" class="text-gray-700 hover:text-blue-600 transition">Send a parcel</a>
                @auth
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
