<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Welcome to your dashboard!</h3>
                    <div class="mt-4 text-gray-600">
                        <p>Here you can manage your account, view your orders, and manage your subscriptions.</p>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-medium text-gray-800">Your Orders</h4>
                            <p class="mt-2 text-sm text-gray-600">View and track your recent orders.</p>
                            <a href="{{ route('orders.index') }}" class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                View Orders
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-medium text-gray-800">Your Profile</h4>
                            <p class="mt-2 text-sm text-gray-600">Update your account information and preferences.</p>
                            <a href="{{ route('profile.edit') }}" class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                Edit Profile
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-medium text-gray-800">Your Subscriptions</h4>
                            <p class="mt-2 text-sm text-gray-600">Manage your active subscriptions.</p>
                            <a href="{{ route('profile.subscriptions') }}" class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                View Subscriptions
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

