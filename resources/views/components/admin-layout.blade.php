<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pawsome') }} Admin - {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-nunito bg-blue-100 text-gray-800">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-500 text-white shadow-lg hidden md:block">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-gray-100 flex items-center">
                    <span class="font-chewy">Pawsome</span>
                    <span class="ml-2 text-sm font-normal">Admin</span>
                </a>
            </div>
            <nav class="mt-5">
                <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.products.*') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products
                    </span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.categories.*') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Categories
                    </span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.orders.*') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Orders
                    </span>
                </a>
                <!-- <a href="{{ route('admin.subscriptions.index') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.subscriptions.*') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Subscriptions
                    </span>
                </a>
                <a href="{{ route('admin.subscriptions.plans') }}" class="block py-2.5 px-6 hover:bg-primary-dark bg-blue-500 {{ request()->routeIs('admin.subscriptions.plans*') ? 'bg-blue-700' : '' }}">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Subscription Plans
                    </span>
                </a> -->
                <div class="mt-10 px-6">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center py-2.5 px-4 rounded bg-red-600 hover:bg-red-700 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="px-4 py-3 flex justify-between items-center">
                    <button class="md:hidden text-gray-500 focus:outline-none" id="sidebarToggle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-2">{{ Auth::guard('admin')->user()->name }}</span>
                        <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::guard('admin')->user()->name }}">
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="container mx-auto">
                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $header ?? $title ?? 'Dashboard' }}</h1>
                    </div>
                    
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    
    <!-- Additional Scripts -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.w-64');
            sidebar.classList.toggle('hidden');
        });
    </script>
    
    @stack('scripts')
</body>
</html>

