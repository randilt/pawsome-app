<x-app-layout>
    <x-slot name="title">Our Products</x-slot>
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-extralight font-chewy mb-4 text-accent">Our Products</h1>

        <div class="flex flex-col md:flex-row gap-8">
           <!-- Filters Column -->
            <div class="md:w-1/4">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Filters</h2>
                    
                    <form id="filter-form" action="{{ route('products.index') }}" method="GET">
                        <div id="filter-parameters" 
                            data-search="{{ request('search') }}" 
                            data-category="{{ request('category_id') }}" 
                            data-min-price="{{ request('min_price') }}" 
                            data-max-price="{{ request('max_price') }}">
                        </div>
                        
                       <!-- Search -->
                        <div class="mb-4">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                       <!-- Category Filter -->
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                       <!-- Price Range -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price Range</label>
                            <div class="flex space-x-2">
                                <input type="number" id="min-price" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                <input type="number" id="max-price" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-opacity-90 transition duration-300">Apply Filters</button>
                            <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-300 text-center">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
           <!-- Products Column -->
            <div class="md:w-3/4">
               <!-- Sort and Limit Options -->
                <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <span class="text-gray-600">{{ $products->total() }} products found</span>
                        </div>
                        {{-- <div class="flex space-x-4">
                            <div>
                                <label for="sort" class="text-sm font-medium text-gray-700 mr-2">Sort by:</label>
                                <select id="sort" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="created_at-desc" {{ request('sort_by') == 'created_at' && request('sort_order') == 'desc' ? 'selected' : '' }}>Newest</option>
                                    <option value="price-asc" {{ request('sort_by') == 'price' && request('sort_order') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price-desc" {{ request('sort_by') == 'price' && request('sort_order') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="name-asc" {{ request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'selected' : '' }}>Name: A-Z</option>
                                </select>
                            </div>
                            <div>
                                <label for="limit" class="text-sm font-medium text-gray-700 mr-2">Show:</label>
                                <select id="limit" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="12" {{ request('limit') == 12 ? 'selected' : '' }}>12</option>
                                    <option value="24" {{ request('limit') == 24 ? 'selected' : '' }}>24</option>
                                    <option value="48" {{ request('limit') == 48 ? 'selected' : '' }}>48</option>
                                </select>
                            </div>
                        </div> --}}
                    </div>
                </div>
                
               <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <a href="{{ route('products.show', $product->id) }}">
                                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/300x300' }}" alt="{{ $product->name }}" class="w-full h-56 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-2 line-clamp-2">{{ $product->description }}</p>
                                    
                                    {{-- Product Rating --}}
                                    @php
                                        $averageRating = 0;
                                        $reviewCount = 0;
                                        try {
                                            $averageRating = App\Models\ProductReview::getProductAverageRating($product->id);
                                            $reviewCount = App\Models\ProductReview::where('product_id', $product->id)->count();
                                        } catch (\Exception $e) {
                                            // Silent fail
                                        }
                                    @endphp
                                    
                                    @if($averageRating > 0)
                                        <div class="flex items-center mb-2">
                                            <div class="flex items-center mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-600">{{ number_format($averageRating, 1) }} ({{ $reviewCount }})</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-primary font-bold">LKR {{ number_format($product->price, 2) }}</span>
                                        <span class="text-sm {{ $product->stock_quantity > 0 ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8">
                            <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
                            <a href="{{ route('products.index') }}" class="text-primary hover:underline mt-2 inline-block">Clear filters</a>
                        </div>
                    @endforelse
                </div>
                
               <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </main>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filter-form');
            const sortSelect = document.getElementById('sort');
            const limitSelect = document.getElementById('limit');
            
            // Handle sort change
            sortSelect.addEventListener('change', function() {
                const [sortBy, sortOrder] = this.value.split('-');
                
                const url = new URL(window.location.href);
                url.searchParams.set('sort_by', sortBy);
                url.searchParams.set('sort_order', sortOrder);
                
                window.location.href = url.toString();
            });
            
            // Handle limit change
            limitSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', this.value);
                
                window.location.href = url.toString();
            });
        });
    </script>
    @endpush
</x-app-layout>