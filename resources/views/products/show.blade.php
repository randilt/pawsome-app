{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('home') }}" class="hover:text-gray-700">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-gray-700">Products</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    @if($product->image_url)
                        <img id="main-image" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                @if(isset($product->variants) && is_array($product->variants) && count($product->variants) > 0)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->variants as $variant)
                            <!-- Additional variant images would go here -->
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="text-3xl font-bold text-primary">LKR {{ number_format($product->price, 2) }}</span>
                    </div>
                </div>

                <!-- Product Rating -->
                @if(isset($averageRating) && $averageRating > 0)
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ number_format($averageRating, 1) }} ({{ isset($reviews) ? $reviews->count() : 0 }} reviews)
                        </span>
                    </div>
                @endif

                <!-- Product Description -->
                <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>

                <!-- Stock Status -->
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                    @if($product->stock_quantity < 20 && $product->stock_quantity > 0)
                        <span class="text-sm text-orange-600 font-medium">
                            Low Stock - Hurry Up! Only {{ $product->stock_quantity }} left
                        </span>
                    @endif
                </div>

                <!-- Product Variants -->
                @if(isset($product->variants) && is_array($product->variants) && count($product->variants) > 0)
                    @foreach($product->variants as $variant)
                        <div class="space-y-2">
                            <label for="variant_{{ $loop->index }}" class="block text-sm font-medium text-gray-700">
                                {{ ucfirst($variant['type']) }}
                            </label>
                            <select name="variant_{{ $variant['type'] }}" 
                                    id="variant_{{ $loop->index }}"
                                    class="variant-select block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                <option value="">Select {{ ucfirst($variant['type']) }}</option>
                                @foreach($variant['values'] as $index => $value)
                                    <option value="{{ $value }}" data-price-adjustment="{{ $variant['price_adjustments'][$index] ?? 0 }}">
                                        {{ $value }}
                                        @if(isset($variant['price_adjustments'][$index]) && $variant['price_adjustments'][$index] > 0)
                                            (+LKR {{ number_format($variant['price_adjustments'][$index], 2) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                @endif

                <!-- Quantity and Add to Cart -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <label for="quantity" class="text-sm font-medium text-gray-700">Quantity</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" class="quantity-decrement px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>-</button>
                            <input type="number" 
                                   id="quantity"
                                   name="quantity" 
                                   data-product-id="{{ $product->id }}"
                                   value="1" 
                                   min="1" 
                                   max="{{ $product->stock_quantity }}"
                                   class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                            <button type="button" class="quantity-increment px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>+</button>
                        </div>
                    </div>

                    <button type="button" 
                            class="add-to-cart-btn w-full bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            data-product-id="{{ $product->id }}"
                            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                        {{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="mt-16 bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Product Description</h2>
            <div class="prose max-w-none text-gray-600">
                {!! nl2br(e($product->long_description)) !!}
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-16 bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
            
            {{-- Reviews Summary --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
                <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8 space-y-4 lg:space-y-0">
                    <div class="text-center lg:text-left">
                        <div class="text-4xl font-bold text-gray-900 mb-2">
                            {{ isset($averageRating) && $averageRating ? number_format($averageRating, 1) : 'N/A' }}
                        </div>
                        <div class="flex items-center justify-center lg:justify-start mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ isset($averageRating) && $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-500">Based on {{ isset($reviews) ? $reviews->count() : 0 }} reviews</p>
                    </div>
                    
                    @auth
                        <button onclick="document.getElementById('review-modal').classList.remove('hidden')" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                            Write a Review
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            Login to Review
                        </a>
                    @endauth
                </div>
                
                {{-- Rating Distribution --}}
                @if(isset($ratingDistribution) && !empty($ratingDistribution) && count($ratingDistribution) > 0)
                    <div class="lg:w-1/3">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating breakdown</h3>
                        @php
                            $ratingCounts = [];
                            $totalReviews = 0;
                            foreach($ratingDistribution as $rating) {
                                $ratingCounts[$rating['_id']] = $rating['count'];
                                $totalReviews += $rating['count'];
                            }
                        @endphp
                        
                        <div class="space-y-2">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $ratingCounts[$i] ?? 0;
                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium w-2">{{ $i }}</span>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <div class="flex-1 h-2 bg-gray-200 rounded">
                                        <div class="h-2 bg-yellow-400 rounded" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500 w-8">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Individual Reviews --}}
            <div class="space-y-6">
                @if(isset($reviews) && $reviews->count() > 0)
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">{{ substr($review['user_name'] ?? 'A', 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $review['user_name'] ?? 'Anonymous' }}</h4>
                                            @if(isset($review['verified_purchase']) && $review['verified_purchase'])
                                                <span class="text-xs text-green-600 font-medium">Verified Purchase</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ isset($review['rating']) && $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-500">
                                                    {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->format('M d, Y') : 'Recently' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(isset($review['title']))
                                        <h5 class="text-sm font-medium text-gray-900 mt-2">{{ $review['title'] }}</h5>
                                    @endif
                                    @if(isset($review['comment']))
                                        <p class="text-sm text-gray-600 mt-2">{{ $review['comment'] }}</p>
                                    @endif
                                </div>
                                
                                <div class="flex-shrink-0">
                                    @if(isset($review['helpful_votes']) && $review['helpful_votes'] > 0)
                                        <p class="text-xs text-gray-500">{{ $review['helpful_votes'] }} people found this helpful</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Review Modal --}}
        @auth
        <div id="review-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Write a Review</h3>
                        <button onclick="document.getElementById('review-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('products.reviews.store', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating_{{ $i }}" class="hidden">
                                    <label for="rating_{{ $i }}" class="cursor-pointer">
                                        <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 rating-star" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="selected-rating" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="review_title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                            <input type="text" name="title" id="review_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" placeholder="Give your review a title">
                        </div>
                        
                        <div class="mb-6">
                            <label for="review_comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                            <textarea name="comment" id="review_comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" placeholder="Share your thoughts about this product" required></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="document.getElementById('review-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-primary-dark">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endauth
        
        {{-- Related Products --}}
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('products.show', $relatedProduct->id) }}">
                                <div class="aspect-square bg-gray-100">
                                    @if($relatedProduct->image_url)
                                        <img src="{{ $relatedProduct->image_url }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">{{ $relatedProduct->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $relatedProduct->description }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-primary">LKR {{ number_format($relatedProduct->price, 2) }}</span>
                                        <span class="text-xs {{ $relatedProduct->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $relatedProduct->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Rating star functionality for review modal
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('selected-rating');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = index + 1;
            ratingInput.value = rating;
            
            // Update star colors
            stars.forEach((s, i) => {
                if (i < rating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
    });
});
</script>
@endsection