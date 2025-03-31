<x-app-layout>
    <x-slot name="title">Order #{{ $order->id }}</x-slot>
    
    <main class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl md:text-5xl font-extralight font-chewy">Order #{{ $order->id }}</h1>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center text-primary hover:text-primary-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Order Details</h2>
                        <p class="text-gray-600">Date: {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-gray-600">Status: 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Shipping Address</h2>
                        <p class="text-gray-600">{{ $order->shipping_address }}</p>
                    </div>
                </div>
                
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Order Items</h2>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $item->product->image_url ?? '/placeholder.svg' }}" alt="{{ $item->product->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">LKR {{ number_format($item->price_at_time, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">LKR {{ number_format($item->price_at_time * $item->quantity, 2) }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-6 text-right">
                    <p class="text-xl font-bold">Total: LKR {{ number_format($order->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>

