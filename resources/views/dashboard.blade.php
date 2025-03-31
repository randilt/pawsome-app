<x-app-layout>
    <x-slot name="title">My Dashboard</x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6">Welcome back, {{ Auth::user()->name }}!</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">My Orders</p>
                                    <p class="text-2xl font-semibold">{{ Auth::user()->orders->count() }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Orders →</a>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">My Subscriptions</p>
                                    <p class="text-2xl font-semibold">{{ Auth::user()->customerSubscriptions->count() }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('profile.subscriptions') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">View Subscriptions →</a>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">My Profile</p>
                                    <p class="text-2xl font-semibold">Settings</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('profile.edit') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Edit Profile →</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
                        
                        @if(Auth::user()->orders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach(Auth::user()->orders->sortByDesc('created_at')->take(5) as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">LKR {{ number_format($order->total_amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($order->status == 'pending')
                                                        <span class="status-badge status-badge-pending">Pending</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="status-badge status-badge-processing">Processing</span>
                                                    @elseif($order->status == 'completed')
                                                        <span class="status-badge status-badge-completed">Completed</span>
                                                    @elseif($order->status == 'cancelled')
                                                        <span class="status-badge status-badge-cancelled">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('orders.show', $order->id) }}" class="text-primary hover:text-primary-dark">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if(Auth::user()->orders->count() > 5)
                                <div class="mt-4 text-right">
                                    <a href="{{ route('orders.index') }}" class="text-primary hover:text-primary-dark text-sm font-medium">View All Orders →</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500">You haven't placed any orders yet.</p>
                                <a href="{{ route('products.index') }}" class="mt-2 inline-block text-primary hover:text-primary-dark">Start Shopping</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">My Subscriptions</h3>
                        
                        @if(Auth::user()->customerSubscriptions->count() > 0)
                            <div class="space-y-4">
                                @foreach(Auth::user()->customerSubscriptions->sortByDesc('created_at')->take(3) as $subscription)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h4 class="font-medium">{{ $subscription->subscriptionPlan->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $subscription->status }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium">LKR {{ number_format($subscription->subscriptionPlan->price, 2) }}</p>
                                                <p class="text-sm text-gray-500">
                                                    @if($subscription->isActive())
                                                        Expires: {{ $subscription->end_date->format('M d, Y') }}
                                                    @else
                                                        Expired: {{ $subscription->end_date->format('M d, Y') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(Auth::user()->customerSubscriptions->count() > 3)
                                <div class="mt-4 text-right">
                                    <a href="{{ route('profile.subscriptions') }}" class="text-primary hover:text-primary-dark text-sm font-medium">View All Subscriptions →</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500">You don't have any active subscriptions.</p>
                                <a href="{{ route('subscriptions.index') }}" class="mt-2 inline-block text-primary hover:text-primary-dark">View Subscription Plans</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

