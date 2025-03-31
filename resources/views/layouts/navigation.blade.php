<nav class="bg-white shadow">
    <div class="container mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-3xl font-chewy text-accent">Pawsome</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ml-10 sm:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-accent text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-accent transition duration-150 ease-in-out">
                        Home
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('products.*') ? 'border-accent text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-accent transition duration-150 ease-in-out">
                        Products
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('subscriptions.*') ? 'border-accent text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-accent transition duration-150 ease-in-out">
                        Subscription
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-accent text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-accent transition duration-150 ease-in-out">
                        About
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact') ? 'border-accent text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-accent transition duration-150 ease-in-out">
                        Contact
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Cart Icon -->
                <a href="{{ route('cart.index') }}" class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cart-count" class="cart-badge">0</span>
                </a>

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.edit') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="{{ route('orders.index') }}">
                                    {{ __('My Orders') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="{{ route('profile.subscriptions') }}">
                                    {{ __('My Subscriptions') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-200"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}"
                                             @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex space-x-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-accent">Log in</a>
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-accent">Register</a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('subscriptions.index') }}" :active="request()->routeIs('subscriptions.index')">
                {{ __('Subscription') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                {{ __('About') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                {{ __('Contact') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index')">
                {{ __('Cart') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>

                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link href="{{ route('orders.index') }}" :active="request()->routeIs('orders.index')">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link href="{{ route('profile.subscriptions') }}" :active="request()->routeIs('profile.subscriptions')">
                        {{ __('My Subscriptions') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}"
                                       @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>

