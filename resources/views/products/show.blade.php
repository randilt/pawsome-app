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
    </script>
</x-app-layout>

        