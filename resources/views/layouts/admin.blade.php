<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pawsome') }} Admin | {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-nunito bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">{{ $header ?? 'Dashboard' }}</h1>
                <a href="{{ route('home') }}" class="underline">Back to home</a>
            </div>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>

