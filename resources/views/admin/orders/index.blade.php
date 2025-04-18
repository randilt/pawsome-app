<x-admin-layout>
    <x-slot name="title">Order Management</x-slot>
    <x-slot name="header">Order Management</x-slot>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
        
        <div class="p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-medium">Orders List</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">LKR {{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800 
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800 
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800 
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800 
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="toggleOrderDetails({{ $order->id }})" class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                                <button onclick="toggleStatusUpdate({{ $order->id }})" class="text-green-600 hover:text-green-900">Update</button>
                            </td>
                        </tr>
                        
                        <!-- Order Details Dropdown -->
                        <tr id="order-details-{{ $order->id }}" class="hidden">
                            <td colspan="6" class="px-6 py-4 bg-gray-50">
                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-medium mb-3">Order Details</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Shipping Address:</h4>
                                            <p class="text-sm">{{ $order->shipping_address }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Order Status:</h4>
                                            <p class="text-sm">{{ ucfirst($order->status) }}</p>
                                        </div>
                                    </div>
                                    
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Items:</h4>
                                    <table class="min-w-full divide-y divide-gray-200 border">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Quantity</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Price</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($order->orderItems as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm">{{ $item->product->name }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-2 text-sm">LKR {{ number_format($item->price_at_time, 2) }}</td>
                                                    <td class="px-4 py-2 text-sm">LKR {{ number_format($item->price_at_time * $item->quantity, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-sm font-medium text-right">Order Total:</td>
                                                <td class="px-4 py-2 text-sm font-medium">LKR {{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Status Update Form Dropdown -->
                        <tr id="status-update-{{ $order->id }}" class="hidden">
                            <td colspan="6" class="px-6 py-4 bg-gray-50">
                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="border rounded-lg p-4">
                                    @csrf
                                    @method('PUT')
                                    <h3 class="text-lg font-medium mb-3">Update Order Status</h3>
                                    
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="toggleStatusUpdate({{ $order->id }})" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-300">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                            Update Status
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $orders->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleOrderDetails(orderId) {
            const detailsRow = document.getElementById(`order-details-${orderId}`);
            const statusRow = document.getElementById(`status-update-${orderId}`);
            
            if (statusRow && !statusRow.classList.contains('hidden')) {
                statusRow.classList.add('hidden');
            }
            
            detailsRow.classList.toggle('hidden');
        }
        
        function toggleStatusUpdate(orderId) {
            const detailsRow = document.getElementById(`order-details-${orderId}`);
            const statusRow = document.getElementById(`status-update-${orderId}`);
            
            if (detailsRow && !detailsRow.classList.contains('hidden')) {
                detailsRow.classList.add('hidden');
            }
            
            statusRow.classList.toggle('hidden');
        }
    </script>
    @endpush
</x-admin-layout>