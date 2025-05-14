@extends('layouts.public')

@section('content')    
<div class="relative bg-cover bg-center bg-no-repeat h-[80vh] flex items-center justify-center" style="background-image: url('{{ asset('images/background.jpg') }}');">
    <!-- Form Container -->
    <div class="rounded-xl p-8 shadow-xl w-full max-w-2xl mx-6 z-10 bg-white/30 backdrop-blur-md">
        <form action="{{ route('package.lookup') }}" method="GET" class="relative z-10 w-full max-w-2xl px-6">
            <h1 class="text-4xl md:text-4xl font-bold text-white drop-shadow text-center mb-6">
                Fast and reliable to you.<br>
                Speed is mandatory.<br>
                Reliability is our duty.
            </h1>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <input
                    type="text"
                    name="code"
                    placeholder="Enter your package code..."
                    class="w-full px-6 py-4 text-lg rounded-md shadow-lg focus:ring-4 focus:ring-blue-500 focus:outline-none bg-white"
                    required>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 text-lg rounded-md shadow-lg transition">
                    Track
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
