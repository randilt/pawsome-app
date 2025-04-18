<x-admin-layout>
    <x-slot name="title">Order #{{ $order->id }}</x-slot>
    <x-slot name="header">Order #{{ $order->id }}</x-slot>
    
    <div class="mb-6 flex justify-between items-center">
        <p class="text-gray-500">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-300">
            &larr; Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium">Order Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer Information</h3>
                            <p class="text-sm"><span class="font-medium">Name:</span> {{ $order->user->name }}</p>
                            <p class="text-sm"><span class="font-medium">Email:</span> {{ $order->user->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Shipping Address</h3>
                            <p class="text-sm">{{ $order->shipping_address }}</p>
                        </div>
                    </div>
                    
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Order Items</h3>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <img src="{{ $item->product->image_url ?? '/placeholder.svg' }}" alt="{{ $item->product->name }}" class="h-10 w-10 rounded-md object-cover mr-3">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                                    <p class="text-xs text-gray-500">ID: {{ $item->product->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">LKR {{ number_format($item->price_at_time, 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900">LKR {{ number_format($item->price_at_time * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-sm font-medium text-right">Order Total:</td>
                                    <td class="px-4 py-3 text-sm font-medium">LKR {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Status and Actions -->
        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium">Order Status</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800 
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800 
                            @elseif($order->status == 'completed') bg-green-100 text-green-800 
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800 
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium">Order Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium">Order Placed</h3>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($order->status != 'pending')
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium">Processing</h3>
                                    <p class="text-xs text-gray-500">Order confirmed and being processed</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($order->status == 'completed')
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium">Completed</h3>
                                    <p class="text-xs text-gray-500">Order has been fulfilled</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($order->status == 'cancelled')
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium">Cancelled</h3>
                                    <p class="text-xs text-gray-500">Order has been cancelled</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>