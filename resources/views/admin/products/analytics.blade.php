<x-admin-layout>
    <x-slot name="title">Product Analytics - {{ $product->name }}</x-slot>
    <x-slot name="header">Product Analytics</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            ← Back to Products
        </a>
    </div>

    {{-- Product Info Card --}}
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg mr-4">
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
                    <p class="text-gray-600">{{ $product->category->name }} • LKR {{ number_format($product->price, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Views (30 days)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['views_last_30_days'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Cart Adds (30 days)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['cart_adds_last_30_days'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Purchases (30 days)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['purchases_last_30_days'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Rating</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ ($analytics['average_rating'] ?? 0) ? number_format($analytics['average_rating'], 1) : 'N/A' }}
                                <span class="text-sm text-gray-500">({{ $analytics['total_reviews'] ?? 0 }} reviews)</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conversion Funnel --}}
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Conversion Funnel (Last 30 Days)</h3>
            
            @if(!empty($analytics['conversion_funnel'] ?? []))
                @php
                    $funnelData = [];
                    foreach($analytics['conversion_funnel'] as $item) {
                        $funnelData[$item['_id']] = $item['count'];
                    }
                    $views = $funnelData['view'] ?? 0;
                    $cartAdds = $funnelData['cart_add'] ?? 0;
                    $purchases = $funnelData['purchase'] ?? 0;
                @endphp
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Product Views</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($views) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-blue-600">100%</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-center text-gray-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-green-900">Added to Cart</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($cartAdds) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-green-600">
                                {{ $views > 0 ? number_format(($cartAdds / $views) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-center text-gray-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-purple-900">Purchases</p>
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($purchases) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-purple-600">
                                {{ $cartAdds > 0 ? number_format(($purchases / $cartAdds) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-500">No conversion data available yet.</p>
            @endif
        </div>
    </div>

    {{-- Rating Distribution --}}
    @if(!empty($analytics['rating_distribution'] ?? []))
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Rating Distribution</h3>
                
                @php
                    $ratingCounts = [];
                    $totalReviews = 0;
                    foreach($analytics['rating_distribution'] as $rating) {
                        $ratingCounts[$rating['_id']] = $rating['count'];
                        $totalReviews += $rating['count'];
                    }
                @endphp
                
                <div class="space-y-3">
                    @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $ratingCounts[$i] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex items-center w-16">
                                <span class="text-sm font-medium text-gray-900">{{ $i }}</span>
                                <svg class="w-4 h-4 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <div class="flex-1 mx-4">
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="w-12 text-right">
                                <span class="text-sm text-gray-600">{{ $count }}</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>