<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">Dashboard</x-slot>
    
   <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Products</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_products'] }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['out_of_stock_products'] }} out of stock</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Orders</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_orders'] }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Users</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_users'] }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-primary">LKR {{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>
    
   <!-- Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-xl font-semibold mb-4">Price Distribution</h3>
        <canvas id="priceChart"></canvas>
    </div>
    
   <!-- Recent Orders -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-primary hover:underline">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['recent_orders'] as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">LKR {{ number_format($order->total_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No recent orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
   <!-- Low Stock Products -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Low Stock Products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-primary hover:underline">View All Products</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['low_stock_products'] as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $product->image_url ?? 'https://via.placeholder.com/40x40' }}" alt="{{ $product->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">LKR {{ number_format($product->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-red-600 font-semibold">{{ $product->stock_quantity }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-primary hover:text-primary-dark">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No low stock products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Price distribution chart
            const ctx = document.getElementById('priceChart').getContext('2d');
            const priceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Rs.0-Rs.200', 'Rs.201-Rs.400', 'Rs.401-Rs.600', 'Rs.601-Rs.800', 'Rs.801-Rs.2000', 'Rs.2000+'],
                    datasets: [{
                        label: 'Number of Products',
                        data: [
                            {{ $priceRanges['0-200'] }},
                            {{ $priceRanges['201-400'] }},
                            {{ $priceRanges['401-600'] }},
                            {{ $priceRanges['601-800'] }},
                            {{ $priceRanges['801-2000'] }},
                            {{ $priceRanges['2000+'] }}
                        ],
                        backgroundColor: '#FF9800',
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>

