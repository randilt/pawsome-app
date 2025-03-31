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
            <div id="product-data" 
                data-id="{{ $product->id }}" 
                data-price="{{ $product->price }}" 
                data-name="{{ $product->name }}" 
                data-image-url="{{ $product->image_url }}" 
                data-description="{{ $product->description }}" 
                style="display: none;">
            </div>
            
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
                    @if($product->stock_quantity< 20 && $product->stock_quantity > 0)
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
                        <span id="quantity" class="text-xl font-semibold">{{ $product->stock_quantity <= 0 ? '0' : '1' }}</span>
                        <button id="increase-quantity" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }} class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">+</button>
                    </div>
                    <input type="hidden" id="max-quantity" value="{{ $product->stock_quantity }}">
                </div>
                
                <button id="add-to-cart" class="w-full bg-accent text-white py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300 {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
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
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeQuantityControls();
            initializeThumbnails();
            initializeAddToCart();
            initializeVariants();
        });
        
        function initializeQuantityControls() {
            const decreaseButton = document.getElementById('decrease-quantity');
            const increaseButton = document.getElementById('increase-quantity');
            const quantityElement = document.getElementById('quantity');
            const maxQuantityElement = document.getElementById('max-quantity');
            
            if (!decreaseButton || !increaseButton || !quantityElement || !maxQuantityElement) {
                return;
            }
            
            const currentStockQty = parseInt(maxQuantityElement.value);
            let quantity = parseInt(quantityElement.textContent) || 1;
            
            decreaseButton.addEventListener('click', () => {
                if (quantity > 1) {
                    quantity--;
                    quantityElement.textContent = quantity;
                    increaseButton.disabled = false;
                }
            });
            
            increaseButton.addEventListener('click', () => {
                if (quantity< currentStockQty) {
                    quantity++;
                    quantityElement.textContent = quantity;
                    if (quantity >= currentStockQty) {
                        increaseButton.disabled = true;
                    }
                }
            });
        }
        
        function initializeThumbnails() {
            const mainImage = document.getElementById('main-image');
            const thumbnails = document.querySelectorAll('.thumbnail');
            
            if (mainImage && thumbnails.length > 0) {
                thumbnails.forEach((thumbnail) => {
                    thumbnail.addEventListener('click', () => {
                        mainImage.src = thumbnail.src;
                    });
                });
            }
        }
        
        function initializeVariants() {
            const variantOptions = document.querySelectorAll('.variant-option');
            const basePrice = parseFloat(document.getElementById('product-data').getAttribute('data-price'));
            
            if (variantOptions.length > 0) {
                variantOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        // Remove active class from all options of the same type
                        document.querySelectorAll(`.variant-option[data-type="${this.dataset.type}"]`).forEach(opt => {
                            opt.classList.remove('border-primary', 'bg-primary', 'text-white');
                        });
                        
                        // Add active class to selected option
                        this.classList.add('border-primary', 'bg-primary', 'text-white');
                        
                        // Update price if needed
                        updatePrice();
                    });
                });
            }
            
            function updatePrice() {
                let totalAdjustment = 0;
                
                // Calculate total price adjustment from all selected variants
                document.querySelectorAll('.variant-option.border-primary').forEach(selected => {
                    totalAdjustment += parseFloat(selected.dataset.priceAdjustment || 0);
                });
                
                // Update displayed price
                const finalPrice = basePrice + totalAdjustment;
                document.querySelector('.text-primary.font-bold').textContent = `LKR ${finalPrice.toFixed(2)}`;
            }
        }
        
        function initializeAddToCart() {
            const addToCartButton = document.getElementById('add-to-cart');
            const quantityElement = document.getElementById('quantity');
            const productData = document.getElementById('product-data');
            
            if (!addToCartButton || !quantityElement || !productData) {
                return;
            }
            
            // Check if the product is already in the cart
            const userCart = JSON.parse(localStorage.getItem('userCart')) || [];
            const productId = productData.getAttribute('data-id');
            const existingProduct = userCart.find(item => item.id === productId);
            
            if (existingProduct) {
                addToCartButton.textContent = 'Added to cart ✓';
                addToCartButton.disabled = true;
                addToCartButton.classList.add('bg-accent/50', 'cursor-not-allowed');
            }
            
            addToCartButton.addEventListener('click', () => {
                const selectedQty = parseInt(quantityElement.textContent);
                if (selectedQty <= 0) {
                    return;
                }
                
                const product = {
                    id: productData.getAttribute('data-id'),
                    name: productData.getAttribute('data-name'),
                    price: parseFloat(document.querySelector('.text-primary.font-bold').textContent.replace('LKR ', '')),
                    imageUrl: productData.getAttribute('data-image-url'),
                    description: productData.getAttribute('data-description'),
                };
                
                updateCart(product, selectedQty);
                
                // Update button state
                addToCartButton.textContent = 'Added to cart ✓';
                addToCartButton.disabled = true;
                addToCartButton.classList.add('bg-accent/50', 'cursor-not-allowed');
                
                // Update cart count in navbar
                updateCartCount();
            });
        }
        
        function updateCart(product, quantity) {
            let userCart = JSON.parse(localStorage.getItem('userCart')) || [];
            
            const existingProductIndex = userCart.findIndex(item => item.id === product.id);
            
            if (existingProductIndex !== -1) {
                userCart[existingProductIndex].quantity += quantity;
            } else {
                userCart.push({ ...product, quantity: quantity });
            }
            
            localStorage.setItem('userCart', JSON.stringify(userCart));
        }
        
        function updateCartCount() {
            const userCart = JSON.parse(localStorage.getItem('userCart')) || [];
            const cartCount = userCart.reduce((total, item) => total + item.quantity, 0);
            
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }
        }
    </script>
    @endpush
</x-app-layout>

