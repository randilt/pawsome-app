<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>
    
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
           <!-- Product Images -->
            <div class="md:w-1/2">
                <div class="mb-4">
                    <img id="main-image" src="{{ $product->image_url ?? 'https://via.placeholder.com/600x600' }}" alt="{{ $product->name }}" class="w-full h-[28rem] object-cover rounded-lg">
                </div>
                @if(isset($product->variants) && is_array($product->variants) && count($product->variants) > 0)
                    <div class="flex space-x-4">
                        @foreach($product->variants as $variant)
                            <img src="{{ $variant['image_url'] ?? 'https://via.placeholder.com/150x150' }}" alt="{{ $product->name }} variant" class="w-24 h-24 object-cover rounded-lg cursor-pointer thumbnail">
                        @endforeach
                    </div>
                @endif
            </div>
            
           <!-- Product Details -->
            <div class="md:w-1/2">
                <h1 class="text-3xl md:text-5xl font-extralight font-chewy mb-4">{{ $product->name }}</h1>
                <p class="text-2xl font-bold text-primary mb-4">
                    LKR {{ number_format($product->price, 2) }}
                </p>
                
                <!-- Average Rating Display -->
                @if(isset($averageRating) && $averageRating > 0)
                    <div class="flex items-center mb-4">
                        <div class="flex items-center mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ number_format($averageRating, 1) }} ({{ isset($reviews) ? $reviews->count() : 0 }} reviews)
                        </span>
                    </div>
                @endif
                
                <p class="text-gray-600 mb-6">{{ $product->description }}</p>

                <p>
                    <span class="inline-block px-3 py-1 mb-4 text-sm font-semibold rounded-full {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                    @if($product->stock_quantity < 20 && $product->stock_quantity > 0)
                        <span class="inline-block px-3 py-1 mb-4 ml-2 text-sm font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                            Low Stock - Hurry Up! Only {{ $product->stock_quantity }} left
                        </span>
                    @endif
                </p>
                
                @if(isset($product->variants) && is_array($product->variants) && count($product->variants) > 0)
                    @foreach($product->variants as $variant)
                        <div class="mb-6">
                            <h2 class="font-semibold mb-2">{{ $variant['type'] }}</h2>
                            <div class="flex space-x-4">
                                @foreach($variant['values'] as $index => $value)
                                    <button class="px-4 py-2 border border-gray-300 rounded-md hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary variant-option" 
                                        data-type="{{ $variant['type'] }}" 
                                        data-value="{{ $value }}" 
                                        data-price-adjustment="{{ $variant['price_adjustments'][$index] ?? 0 }}">
                                        {{ $value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
                
                <div class="mb-6">
                    <h2 class="font-semibold mb-2">Quantity</h2>
                    <div class="flex items-center space-x-4">
                        <button id="decrease-quantity" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }} class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">-</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-16 text-center border-gray-300 rounded-md" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                        <button id="increase-quantity" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }} class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">+</button>
                    </div>
                </div>
                
                <button id="add-to-cart-btn" data-product-id="{{ $product->id }}" class="w-full bg-blue-500 text-white py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300 {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                    {{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                </button>
            </div>
        </div>
        
       <!-- Product Description -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-4">Product Description</h2>
            <p class="text-gray-600 mb-4">{!! nl2br(e($product->long_description)) !!}</p>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
            
            {{-- Reviews Summary --}}
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="text-4xl font-bold text-gray-900 mr-4">
                            {{ isset($averageRating) && $averageRating ? number_format($averageRating, 1) : 'N/A' }}
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ isset($averageRating) && $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600">Based on {{ isset($reviews) ? $reviews->count() : 0 }} reviews</p>
                        </div>
                    </div>
                    
                    @auth
                        <button onclick="openReviewModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Write a Review
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Login to Review
                        </a>
                    @endauth
                </div>
                
                {{-- Rating Distribution --}}
                @if(isset($ratingDistribution) && !empty($ratingDistribution) && count($ratingDistribution) > 0)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Rating breakdown</h4>
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
                                <div class="flex items-center text-sm">
                                    <span class="w-3">{{ $i }}</span>
                                    <svg class="w-4 h-4 text-yellow-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <div class="flex-1 mx-2">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                    <span class="w-8 text-right text-gray-600">{{ $count }}</span>
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
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h4 class="font-medium text-gray-900 mr-3">{{ $review['user_name'] ?? 'Anonymous' }}</h4>
                                        @if(isset($review['verified_purchase']) && $review['verified_purchase'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verified Purchase
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ isset($review['rating']) && $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600">
                                            {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->format('M d, Y') : 'Recently' }}
                                        </span>
                                    </div>
                                    
                                    @if(isset($review['title']))
                                        <h5 class="font-medium text-gray-900 mb-2">{{ $review['title'] }}</h5>
                                    @endif
                                    @if(isset($review['comment']))
                                        <p class="text-gray-700">{{ $review['comment'] }}</p>
                                    @endif
                                </div>
                                
                                <div class="text-right text-sm text-gray-500">
                                    @if(isset($review['helpful_votes']) && $review['helpful_votes'] > 0)
                                        <p>{{ $review['helpful_votes'] }} people found this helpful</p>
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
        <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Write a Review</h3>
                        <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" 
                                            class="rating-star w-8 h-8 text-gray-300 hover:text-yellow-400 focus:outline-none">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                            <input type="text" id="title" name="title" required maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Summarize your review in a few words">
                        </div>
                        
                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                            <textarea id="comment" name="comment" rows="4" required minlength="10"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Tell others about your experience with this product"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeReviewModal()" 
                                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endauth
        
       <!-- Related Products -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-4">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <a href="{{ route('products.show', $relatedProduct->id) }}">
                                <img src="{{ $relatedProduct->image_url ?? 'https://via.placeholder.com/300x300' }}" alt="{{ $relatedProduct->name }}" class="w-full h-56 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $relatedProduct->name }}</h3>
                                    <p class="text-gray-600 mb-2 line-clamp-2">{{ $relatedProduct->description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-primary font-bold">LKR {{ number_format($relatedProduct->price, 2) }}</span>
                                        <span class="text-sm {{ $relatedProduct->stock_quantity > 0 ? 'text-green-500' : 'text-red-500' }}">
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
    </main>
    
    <script>
        // Direct script inclusion to ensure it runs
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM fully loaded");
            
            // Initialize quantity controls
            const decreaseButton = document.getElementById('decrease-quantity');
            const increaseButton = document.getElementById('increase-quantity');
            const quantityInput = document.getElementById('quantity');
            
            if (decreaseButton && increaseButton && quantityInput) {
                console.log("Quantity controls found");
                const maxQuantity = parseInt(quantityInput.getAttribute('max') || '100');
                
                decreaseButton.addEventListener('click', function() {
                    console.log("Decrease clicked");
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });
                
                increaseButton.addEventListener('click', function() {
                    console.log("Increase clicked");
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue < maxQuantity) {
                        quantityInput.value = currentValue + 1;
                    }
                });
                
                quantityInput.addEventListener('change', function() {
                    let currentValue = parseInt(quantityInput.value);
                    if (isNaN(currentValue) || currentValue < 1) {
                        quantityInput.value = 1;
                    } else if (currentValue > maxQuantity) {
                        quantityInput.value = maxQuantity;
                    }
                });
            } else {
                console.log("Quantity controls not found");
            }
            
            // Initialize thumbnails
            const mainImage = document.getElementById('main-image');
            const thumbnails = document.querySelectorAll('.thumbnail');
            
            if (mainImage && thumbnails.length > 0) {
                console.log("Thumbnails found");
                thumbnails.forEach((thumbnail) => {
                    thumbnail.addEventListener('click', function() {
                        mainImage.src = this.src;
                    });
                });
            }
            
            // Initialize variants
            const variantOptions = document.querySelectorAll('.variant-option');
            
            if (variantOptions.length > 0) {
                console.log("Variants found");
                variantOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        const type = this.getAttribute('data-type');
                        document.querySelectorAll(`.variant-option[data-type="${type}"]`).forEach(opt => {
                            opt.classList.remove('border-primary', 'bg-primary', 'text-white');
                        });
                        
                        this.classList.add('border-primary', 'bg-primary', 'text-white');
                    });
                });
            }
            
            // Initialize add to cart button
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            
            if (addToCartBtn) {
                console.log("Add to cart button found");
                addToCartBtn.addEventListener('click', function() {
                    console.log("Add to cart clicked");
                    
                    const productId = this.getAttribute('data-product-id');
                    const quantity = parseInt(quantityInput ? quantityInput.value : 1);
                    
                    if (!productId || isNaN(quantity) || quantity <= 0) {
                        console.log("Invalid product ID or quantity");
                        return;
                    }
                    
                    // Get product details
                    const productName = document.querySelector('h1').textContent.trim();
                    const productPriceText = document.querySelector('.text-primary.font-bold').textContent.trim();
                    const productPrice = parseFloat(productPriceText.replace('LKR ', '').replace(/,/g, ''));
                    const productImage = document.getElementById('main-image').getAttribute('src');
                    const productDescription = document.querySelector('.text-gray-600').textContent.trim();
                    
                    console.log("Product details:", {
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage,
                        description: productDescription,
                        quantity: quantity
                    });
                    
                    // Add to cart
                    let cart = JSON.parse(localStorage.getItem('userCart')) || [];
                    
                    // Check if product already exists in cart
                    const existingProductIndex = cart.findIndex(item => item.id === productId);
                    
                    if (existingProductIndex !== -1) {
                        // Update quantity if product already exists
                        cart[existingProductIndex].quantity += quantity;
                    } else {
                        // Add new product to cart
                        cart.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            imageUrl: productImage,
                            description: productDescription,
                            quantity: quantity
                        });
                    }
                    
                    // Save updated cart to localStorage
                    localStorage.setItem('userCart', JSON.stringify(cart));
                    console.log("Cart updated:", cart);
                    
                    // Update cart count
                    updateCartCount();
                    
                    // Show notification
                    showNotification('Product added to cart!', 'success');
                });
            } else {
                console.log("Add to cart button not found");
            }
            
            // Update cart count
            updateCartCount();
        });
        
        // Update cart count in header
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('userCart')) || [];
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            
            console.log("Updating cart count:", totalItems);
            
            // Update all cart count elements
            document.querySelectorAll('.cart-count').forEach(element => {
                element.textContent = totalItems;
            });
        }
        
        // Show notification
        function showNotification(message, type = 'success') {
            console.log("Showing notification:", message, type);
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Review modal functions
        let selectedRating = 0;

        function openReviewModal() {
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('reviewForm').reset();
            selectedRating = 0;
            updateStarDisplay();
        }

        function setRating(rating) {
            selectedRating = rating;
            document.getElementById('rating').value = rating;
            updateStarDisplay();
        }

        function updateStarDisplay() {
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                if (index < selectedRating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        function submitReview(event) {
            event.preventDefault();
            
            if (selectedRating === 0) {
                alert('Please select a rating');
                return;
            }
            
            const formData = new FormData(event.target);
            const submitButton = event.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';
            
            fetch(`/products/{{ $product->id }}/reviews`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeReviewModal();
                    showNotification('Review submitted successfully!', 'success');
                    // Refresh page to show new review
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while submitting your review', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Review';
            });
        }
    </script>
</x-app-layout>