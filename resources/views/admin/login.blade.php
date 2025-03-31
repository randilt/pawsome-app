&lt;!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pawsome') }} Admin Login</title>

   <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">

   <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1615789591457-74a63395c990?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="font-nunito">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 max-w-full">
            <div class="text-center mb-8">
                <h1 class="text-5xl text-gray-800 font-chewy">Pawsome Admin Login</h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#FF9800] focus:border-[#FF9800]">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#FF9800] focus:border-[#FF9800]">
                </div>
                <button type="submit"
                    class="w-full bg-[#FF9800] text-white py-2 px-4 rounded-md hover:bg-[#F57C00] focus:outline-none focus:ring-2 focus:ring-[#FF9800] focus:ring-opacity-50 transition duration-300">
                    Login
                </button>
            </form>
            <div class="mt-8 text-center text-sm text-gray-500">
                <a href="{{ route('home') }}" class="underline">Back to home</a>
            </div>

            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Pawsome Pet Supplies &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

